<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;

class GoogleSheetsInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-sheets:init {--sync-existing : Sync all existing completed transactions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize Google Sheets integration for transaction backup';

    protected $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        parent::__construct();
        $this->googleSheetsService = $googleSheetsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing Google Sheets integration...');

        // Initialize sheet with headers
        $this->googleSheetsService->initializeSheet();
        $this->info('✓ Google Sheet initialized with headers');

        // Sync existing transactions if requested
        if ($this->option('sync-existing')) {
            $this->info('Syncing existing transactions...');

            $completedTransactions = Transaction::whereNotNull('exit_time')
                ->with('vehicle', 'area')
                ->orderBy('exit_time')
                ->get();

            $this->info("Found {$completedTransactions->count()} completed transactions to sync");

            $bar = $this->output->createProgressBar($completedTransactions->count());

            foreach ($completedTransactions as $transaction) {
                $this->googleSheetsService->syncTransaction($transaction);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('✓ All existing transactions synced to Google Sheets');
        }

        $this->info('✓ Google Sheets integration setup complete!');
        $this->info('Future transactions will be automatically synced when completed.');
    }
}
