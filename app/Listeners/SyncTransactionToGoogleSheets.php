<?php

namespace App\Listeners;

use App\Events\TransactionCompleted;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\Log;

class SyncTransactionToGoogleSheets
{

    protected $googleSheetsService;

    /**
     * Create the event listener.
     */
    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionCompleted $event): void
    {
        $transaction = $event->transaction;

        Log::info('SyncTransactionToGoogleSheets listener triggered', [
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'timestamp' => now()->toISOString(),
            'trace_id' => uniqid()
        ]);

        try {
            // Ensure transaction has all relations loaded
            if (!$transaction->relationLoaded('vehicle')) {
                $transaction->load('vehicle');
            }
            if (!$transaction->relationLoaded('area')) {
                $transaction->load('area');
            }

            Log::info('Syncing transaction to Google Sheets', [
                'transaction_id' => $transaction->id,
                'plate' => $transaction->plate_number,
                'status' => $transaction->status
            ]);

            $this->googleSheetsService->syncTransaction($transaction);
        } catch (\Exception $e) {
            Log::error('Error in SyncTransactionToGoogleSheets listener', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
