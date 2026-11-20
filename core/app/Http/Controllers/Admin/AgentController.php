<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * E-pin agents: members who buy pins at volume prices and earn the
     * seller bonus when their pins pay for a plan purchase.
     */
    public function index()
    {
        $pageTitle = 'E-Pin Agents';

        $agents = User::where('is_epin_agent', 1)
            ->withCount(['generatedPins as total_pins'])
            ->withCount(['generatedPins as used_pins' => fn ($q) => $q->used()])
            ->withCount(['generatedPins as unused_pins' => fn ($q) => $q->unused()])
            ->withSum('generatedPins as pins_volume', 'amount')
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('admin.agent.index', compact('pageTitle', 'agents'));
    }

    /**
     * Grant or revoke the agent flag for a user id. A pure flag flip:
     * no money moves, and a revoked agent simply stops earning the
     * seller bonus and loses volume pricing on the next generation.
     */
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        $user->is_epin_agent = $user->is_epin_agent ? 0 : 1;
        $user->save();

        $notify[] = $user->is_epin_agent
            ? ['success', 'Agent status granted to ' . ($user->username ?: $user->fullname)]
            : ['success', 'Agent status revoked for ' . ($user->username ?: $user->fullname)];
        return back()->withNotify($notify);
    }

    /**
     * Save the seller bonus percent (0 to 10) and the volume discount
     * tiers (qty:percent comma separated, ascending qty, percent 0 to 50).
     */
    public function settings(Request $request)
    {
        $request->validate([
            'epin_seller_bonus_percent' => 'required|numeric|min:0|max:10',
            'epin_agent_discounts'      => 'nullable|string|max:255',
        ]);

        [$tiers, $error] = self::parseTierString($request->epin_agent_discounts);
        if ($error) {
            $notify[] = ['error', $error];
            return back()->withNotify($notify)->withInput();
        }

        // saved through the model so the GeneralSetting boot hook clears
        // the gs() cache in the same request
        $general                          = GeneralSetting::firstOrNew();
        $general->epin_seller_bonus_percent = round((float) $request->epin_seller_bonus_percent, 2);
        $general->epin_agent_discounts    = self::tierString($tiers);
        $general->save();

        $notify[] = ['success', 'E-pin agent settings saved successfully'];
        return back()->withNotify($notify);
    }

    /**
     * The configured discount tiers, normalized and sorted by ascending
     * quantity. Malformed entries are skipped silently (the admin panel
     * validates before writing, this reader just stays defensive).
     *
     * @return array[] each: ['qty' => int, 'percent' => float]
     */
    public static function tiers(): array
    {
        [$tiers] = self::parseTierString((string) gs('epin_agent_discounts'));
        return $tiers;
    }

    /**
     * Best volume discount percent for a pin quantity. A quantity meets a
     * tier when it is greater than or equal to the tier quantity.
     *
     * @param int $qty
     * @return float
     */
    public static function discountPercentFor(int $qty): float
    {
        $percent = 0;
        foreach (self::tiers() as $tier) {
            if ($qty >= $tier['qty']) {
                $percent = max($percent, $tier['percent']);
            }
        }
        return $percent;
    }

    /**
     * Parse a 'qty:percent,qty:percent' string. Returns [tiers, error]:
     * on error tiers is empty and error explains the problem.
     *
     * @param string $raw
     * @return array [array[] $tiers, string|null $error]
     */
    private static function parseTierString($raw): array
    {
        $raw   = trim((string) $raw);
        $tiers = [];

        if ($raw === '') {
            return [[], null];
        }

        foreach (explode(',', $raw) as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '') {
                return [[], 'Discount tiers: empty tier found. Use the format qty:percent, e.g. 50:3,100:5,500:8'];
            }
            $parts = explode(':', $chunk);
            if (count($parts) != 2 || trim($parts[0]) === '' || trim($parts[1]) === '') {
                return [[], "Discount tier '{$chunk}' is invalid. Use qty:percent, e.g. 50:3"];
            }
            $qty = trim($parts[0]);
            $pct = trim($parts[1]);
            if (!ctype_digit($qty) || (int) $qty < 1) {
                return [[], "Discount tier '{$chunk}': quantity must be a whole number of at least 1"];
            }
            if (!is_numeric($pct) || (float) $pct < 0 || (float) $pct > 50) {
                return [[], "Discount tier '{$chunk}': percent must be a number between 0 and 50"];
            }
            $tiers[] = ['qty' => (int) $qty, 'percent' => round((float) $pct, 2)];
        }

        usort($tiers, fn ($a, $b) => $a['qty'] <=> $b['qty']);
        for ($i = 1; $i < count($tiers); $i++) {
            if ($tiers[$i]['qty'] == $tiers[$i - 1]['qty']) {
                return [[], 'Discount tiers: quantity ' . $tiers[$i]['qty'] . ' is configured twice. Quantities must ascend'];
            }
        }

        return [$tiers, null];
    }

    /**
     * Canonical tier string for storage, e.g. '50:3,100:5,500:8'.
     *
     * @param array[] $tiers
     * @return string
     */
    private static function tierString(array $tiers): string
    {
        $parts = [];
        foreach ($tiers as $tier) {
            $percent = rtrim(rtrim(number_format($tier['percent'], 2, '.', ''), '0'), '.');
            $parts[] = $tier['qty'] . ':' . $percent;
        }
        return implode(',', $parts);
    }
}
