<?php

namespace App\Lib;

use Illuminate\Support\Facades\DB;

/**
 * Defers non-critical work (member notifications, admin notifications)
 * until the surrounding database transaction has committed.
 *
 * Money transactions hold row locks (users, user_extras, settings) while
 * they run. notify() writes DB rows and can send mail/SMS/push, so calling
 * it inside a transaction extends those locks by the mail round-trip time.
 * Wrap such calls in AfterCommit::push() and call AfterCommit::run() right
 * after the DB::transaction() returns.
 *
 * push() auto-detects its context: inside a transaction the callable is
 * queued, outside one it runs immediately  -  so call sites stay uniform.
 * If run() is never called after a commit, queued notifications are dropped:
 * they are deliberately non-critical and must never break the money flow.
 */
class AfterCommit
{
    /**
     * @var array<int, callable>
     */
    protected static array $queue = [];

    public static function push(callable $work): void
    {
        if (DB::transactionLevel() > 0) {
            static::$queue[] = $work;
            return;
        }

        $work();
    }

    /**
     * Drain and execute everything queued during the last transaction.
     * A failing item never blocks the rest  -  each runs on its own.
     */
    public static function run(): void
    {
        $queued = static::$queue;
        static::$queue = [];

        foreach ($queued as $work) {
            try {
                $work();
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
