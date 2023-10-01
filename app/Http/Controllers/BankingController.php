<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BankingController extends Controller
{
    public function deposit(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $user = User::findOrFail($request->input('user_id'));

            $feeRate = ($user->account_type === 'Business') ? 0.025 : 0.015;
            $fee = $request->input('amount') * $feeRate;

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->transaction_type = 'deposit';
            $transaction->amount = $request->input('amount');
            $transaction->fee = $fee;
            $transaction->date = Carbon::now();
            $transaction->save();

            $user->balance += ($request->input('amount') - $fee);
            $user->save();
            return response()->json(['message' => 'Deposit successful']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showTransactionsAndBalance(Request $request)
    {
        try {
            $user = auth()->user();
            $transactions = Transaction::where('user_id', $user->id)->get();

            $currentBalance = $user->balance;

            return response()->json(['transactions' => $transactions, 'balance' => $currentBalance]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showDepositedTransactions(Request $request)
    {
        try {
            $user = auth()->user();
            $depositedTransactions = Transaction::where('user_id', $user->id)
                ->where('transaction_type', 'deposit')
                ->get();

            return response()->json(['deposited_transactions' => $depositedTransactions]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showWithdrawalTransactions(Request $request)
    {
        try {
            $user = auth()->user();
            $withdrawalTransactions = Transaction::where('user_id', $user->id)
                ->where('transaction_type', 'withdrawal')
                ->get();

            return response()->json(['withdrawal_transactions' => $withdrawalTransactions]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function withdrawal(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $user = User::findOrFail($request->input('user_id'));

            $isFreeWithdrawal = $this->isFreeWithdrawal($user, $request->input('amount'));

            $feeRate = ($user->account_type === 'Business') ? 0.025 : 0.015;
            $fee = $isFreeWithdrawal ? 0 : ($request->input('amount') * $feeRate);

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->transaction_type = 'withdrawal';
            $transaction->amount = $request->input('amount');
            $transaction->fee = $fee;
            $transaction->date = Carbon::now();
            $transaction->save();

            $user->balance -= ($request->input('amount') + $fee);
            $user->save();

            return response()->json(['message' => 'Withdrawal successful']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function isFreeWithdrawal($user, $amount)
    {
        try {
            if ($user->account_type === 'Individual') {
                $today = Carbon::now()->format('l');

                if ($today === 'Friday' || $amount <= 1000) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}