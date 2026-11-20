<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\Pin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InternalEventController extends Controller
{
    public function index()
    {
        $pageTitle = 'Company Events';
        $events    = Event::where('event_type', 2)->where('status', 1)
            ->with('category', 'city')->latest()->paginate(9);
        return view('Template::user.internal_events', compact('pageTitle', 'events'));
    }

    public function details($slug, $id)
    {
        $event = Event::where('event_type', 2)->where('status', 1)
            ->with('category', 'city', 'user')->findOrFail($id);
        $pageTitle     = $event->title;
        $participation = EventParticipation::where('event_id', $event->id)
            ->where('user_id', auth()->id())->first();
        return view('Template::user.internal_event_details', compact('pageTitle', 'event', 'participation'));
    }

    /**
     * Company event registration: free events register directly; paid events
     * accept wallet balance or a valid E-Pin. Zero affiliate commission.
     */
    public function register(Request $request, $id)
    {
        $event = Event::where('event_type', 2)->where('status', 1)->findOrFail($id);

        // The duplicate guard, the payment and the registration share one
        // transaction with a locked participation row, so a double submit can
        // never charge twice and a failed write rolls the payment back.
        try {
            DB::transaction(function () use ($request, $event, &$pinCode, &$trx) {
                $existing = EventParticipation::where('event_id', $event->id)
                    ->where('user_id', auth()->id())
                    ->where('status', 'joined')
                    ->lockForUpdate()
                    ->first();
                if ($existing) {
                    $pinCode = 'duplicate';
                    return;
                }

                $user = auth()->user();
                $trx  = getTrx();

                if ($event->price > 0) {
                    if ($request->pay_method == 'wallet') {
                        $deducted = User::where('id', $user->id)
                            ->where('balance', '>=', $event->price)->decrement('balance', $event->price);
                        if (!$deducted) {
                            $pinCode = 'insufficient';
                            return;
                        }
                        $user->refresh();
                        $transaction               = new Transaction();
                        $transaction->user_id      = $user->id;
                        $transaction->amount       = $event->price;
                        $transaction->post_balance = $user->balance;
                        $transaction->trx_type     = '-';
                        $transaction->details      = 'Company event registration: ' . $event->title;
                        $transaction->remark       = 'internal_event';
                        $transaction->trx          = $trx;
                        $transaction->save();
                    } elseif ($request->pay_method == 'epin') {
                        $claimed = Pin::where('pin', $request->epin)
                            ->where('status', Status::PIN_UNUSED)
                            ->where('amount', '>=', $event->price)
                            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                            ->update(['status' => Status::PIN_USED, 'user_id' => $user->id]);
                        if (!$claimed) {
                            $pinCode = 'badpin';
                            return;
                        }
                        $pin = Pin::where('pin', $request->epin)->first();
                        if ($pin->generate_user_id == $user->id) {
                            Pin::where('pin', $request->epin)->update(['status' => Status::PIN_UNUSED, 'user_id' => null]);
                            $pinCode = 'selfpin';
                            return;
                        }
                        // e-pins are an external medium  -  no wallet balance
                        // moves, so this row is informational (trx_type NULL);
                        // writing '-' would break the ledger replay invariant
                        $transaction               = new Transaction();
                        $transaction->user_id      = $user->id;
                        $transaction->amount       = $event->price;
                        $transaction->post_balance = $user->balance;
                        $transaction->trx_type     = null;
                        $transaction->details      = 'Company event registration (e-pin ' . $pin->pin . '): ' . $event->title;
                        $transaction->remark       = 'internal_event';
                        $transaction->trx          = $trx;
                        $transaction->save();
                    } else {
                        $pinCode = 'nomethod';
                        return;
                    }
                }

                EventParticipation::updateOrCreate(
                    ['event_id' => $event->id, 'user_id' => auth()->id()],
                    ['status' => 'joined', 'paid_amount' => $event->price, 'updated_at' => now()]
                );
            });
        } catch (\Throwable $e) {
            $notify[] = ['error', 'Registration could not be completed. Please try again.'];
            return back()->withNotify($notify);
        }

        if (isset($pinCode)) {
            $messages = [
                'duplicate'    => 'You have already registered for this event',
                'insufficient' => 'Insufficient wallet balance for this event',
                'badpin'       => 'Invalid, already-used, or insufficient E-Pin for this event',
                'selfpin'      => 'You cannot use a pin generated by your own account',
                'nomethod'     => 'Please choose a payment method',
            ];
            $notify[] = ['error', $messages[$pinCode]];
            return back()->withNotify($notify);
        }

        $user = auth()->user();
        notify($user, 'EVENT_REGISTER', [
            'event_name' => $event->title,
            'start_time' => showDateTime($event->start_time),
        ]);

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New company event registration: ' . $event->title;
        $adminNotification->click_url = route('admin.events.internal.edit', $event->id);
        $adminNotification->save();

        $notify[] = ['success', 'Registration successful. See you at the event!'];
        return back()->withNotify($notify);
    }
}
