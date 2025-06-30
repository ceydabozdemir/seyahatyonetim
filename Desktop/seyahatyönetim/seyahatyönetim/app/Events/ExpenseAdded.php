<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpenseAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $expense;

    public function __construct($expense)
    {
        $this->expense = $expense;
        Log::info('ExpenseAdded event triggered', ['expense' => $expense->toArray()]);
    }
}
