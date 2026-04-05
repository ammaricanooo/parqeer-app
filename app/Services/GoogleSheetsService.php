<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Google\Service\Sheets\ClearValuesRequest;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $credentialsPath = storage_path('app/google-credentials.json');

        if (!file_exists($credentialsPath)) {
            throw new \Exception("Google credentials file not found at: {$credentialsPath}");
        }

        $this->client = new Client();

        // --- FIX SSL DISINI ---
        // Buat Guzzle Client baru dengan opsi verify false
        $guzzleClient = new \GuzzleHttp\Client([
            'verify' => false
        ]);
        // Pasang ke Google Client
        $this->client->setHttpClient($guzzleClient);
        // -----------------------

        $this->client->setApplicationName('Parqeer Parking System');
        $this->client->setScopes([Sheets::SPREADSHEETS]);
        $this->client->setAuthConfig($credentialsPath);
        $this->client->setAccessType('offline');

        $this->service = new Sheets($this->client);
    }

    /**
     * Format Header (Baris 1) jadi Hitam Teks Putih
     */
    public function formatHeader()
    {
        try {
            $requests = [
                new Request([
                    'repeatCell' => [
                        'range' => [
                            'sheetId' => 0,
                            'startRowIndex' => 0,
                            'endRowIndex' => 1,
                            'startColumnIndex' => 0,
                            'endColumnIndex' => 8
                        ],
                        'cell' => [
                            'userEnteredFormat' => [
                                'backgroundColor' => ['red' => 0.0, 'green' => 0.0, 'blue' => 0.0],
                                'textFormat' => [
                                    'foregroundColor' => ['red' => 1.0, 'green' => 1.0, 'blue' => 1.0],
                                    'bold' => true
                                ],
                                'horizontalAlignment' => 'CENTER'
                            ]
                        ],
                        'fields' => 'userEnteredFormat(backgroundColor,textFormat,horizontalAlignment)'
                    ]
                ])
            ];

            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest(['requests' => $requests]);
            $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
        } catch (\Exception $e) {
            Log::error('Format Error: ' . $e->getMessage());
        }
    }

    /**
     * Format total row yang berada di bawah data
     */
    protected function formatTotalRow($rowIndex)
    {
        try {
            $requests = [
                new Request([
                    'repeatCell' => [
                        'range' => [
                            'sheetId' => 0,
                            'startRowIndex' => $rowIndex - 1,
                            'endRowIndex' => $rowIndex,
                            'startColumnIndex' => 0,
                            'endColumnIndex' => 8
                        ],
                        'cell' => [
                            'userEnteredFormat' => [
                                'backgroundColor' => ['red' => 0.0, 'green' => 0.0, 'blue' => 0.0],
                                'textFormat' => [
                                    'foregroundColor' => ['red' => 1.0, 'green' => 1.0, 'blue' => 1.0],
                                    'bold' => true
                                ],
                                'horizontalAlignment' => 'CENTER'
                            ]
                        ],
                        'fields' => 'userEnteredFormat(backgroundColor,textFormat,horizontalAlignment)'
                    ]
                ])
            ];

            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest(['requests' => $requests]);
            $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
        } catch (\Exception $e) {
            Log::error('Format Total Row Error: ' . $e->getMessage());
        }
    }

    /**
     * Auto Resize Kolom
     */
    public function autoResizeColumns()
    {
        try {
            $requests = [new Request(['autoResizeDimensions' => ['dimensions' => ['sheetId' => 0, 'dimension' => 'COLUMNS', 'startIndex' => 0, 'endIndex' => 8]]])];
            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest(['requests' => $requests]);
            $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
        } catch (\Exception $e) {
            Log::error('Resize Error: ' . $e->getMessage());
        }
    }



    /**
     * Sync transaction data to Google Sheets
     */
    public function syncTransaction($transaction)
    {
        try {
            // Check if this transaction has already been synced to prevent duplicates
            $cacheKey = "gs_sync_{$transaction->id}";
            if (Cache::has($cacheKey)) {
                Log::info('Transaction already synced to Google Sheets, skipping duplicate', [
                    'transaction_id' => $transaction->id
                ]);
                return;
            }

            // Load relations if not loaded
            if (!$transaction->relationLoaded('vehicle')) {
                $transaction->load('vehicle');
            }
            if (!$transaction->relationLoaded('area')) {
                $transaction->load('area');
            }

            // Prepare data in same format as Excel export
            $durationText = '-';
            if ($transaction->duration_minutes) {
                $hours = floor($transaction->duration_minutes / 60);
                $minutes = $transaction->duration_minutes % 60;
                $durationText = $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";
            }

            $rowData = [
                strtoupper($transaction->vehicle->plate_number ?? '-'),
                strtoupper($transaction->vehicle->type ?? '-'),
                $transaction->area->name ?? '-',
                $transaction->entry_time ? \Carbon\Carbon::parse($transaction->entry_time)->format('d/m/Y H:i') : '-',
                $transaction->exit_time ? \Carbon\Carbon::parse($transaction->exit_time)->format('d/m/Y H:i') : '-',
                $durationText,
                (int)($transaction->amount ?? 0),
                $transaction->id, // Transaction ID untuk tracking
            ];

            // Append new row
            $this->appendRow($rowData);

            // Mark as synced in cache for 24 hours to prevent duplicates
            Cache::put($cacheKey, true, 86400);

            Log::info('Transaction synced to Google Sheets', ['transaction_id' => $transaction->id]);
        } catch (\Exception $e) {
            Log::error('Failed to sync transaction to Google Sheets', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Find row number for a transaction ID
     */
    protected function findTransactionRow($transactionId)
    {
        try {
            // Get all data from sheet
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, 'Sheet1!A:A');
            $values = $response->getValues();

            // Look for transaction ID in a hidden column or use row number as ID
            // For simplicity, we'll use row numbers and assume transactions are appended in order
            // In a real implementation, you might want to add a transaction ID column

            return null; // For now, always append (you can implement proper lookup later)

        } catch (\Exception $e) {
            Log::error('Failed to find transaction row', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Append a new row to the sheet
     */
    protected function appendRow($data)
    {
        try {
            $body = new ValueRange([
                'values' => [$data]
            ]);

            $params = [
                'valueInputOption' => 'RAW'
            ];

            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                'Sheet1!A:H',
                $body,
                ['valueInputOption' => 'RAW']
            );

            Log::info('Row appended to Google Sheets', ['data_count' => count($data)]);
        } catch (\Exception $e) {
            Log::error('Failed to append row to Google Sheets', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


    /**
     * Hapus total row yang lama
     */
    protected function clearTotalRow()
    {
        try {
            // Cari baris yang mengandung "TOTAL PENDAPATAN"
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, 'Sheet1!F:F');
            $columnF = $response->getValues() ?? [];

            $totalRowIndices = [];
            foreach ($columnF as $index => $cell) {
                if (isset($cell[0]) && str_contains($cell[0], 'TOTAL PENDAPATAN')) {
                    $totalRowIndices[] = $index + 1; // +1 karena array dimulai dari 0
                }
            }

            // Hapus semua total rows kecuali yang terakhir
            if (count($totalRowIndices) > 1) {
                foreach (array_slice($totalRowIndices, 0, -1) as $rowIndex) {
                    // Clear baris tersebut
                    $this->service->spreadsheets_values->clear(
                        $this->spreadsheetId,
                        "Sheet1!A{$rowIndex}:H{$rowIndex}",
                        new ClearValuesRequest()
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Clear Total Row Failed: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing row
     */
    protected function updateRow($range, $data, $inputOption = 'RAW')
    {
        $body = new ValueRange([
            'values' => $data
        ]);

        $params = [
            'valueInputOption' => $inputOption
        ];

        $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }

    /**
     * Initialize the Google Sheet with headers
     */
    public function initializeSheet()
    {
        try {
            $headers = [
                'NO. PLAT',
                'JENIS',
                'AREA LOKASI',
                'WAKTU MASUK',
                'WAKTU KELUAR',
                'DURASI',
                'NILAI TARIF (IDR)',
                'TRANSACTION ID' // Hidden column for tracking
            ];

            $body = new ValueRange([
                'values' => [$headers]
            ]);

            $params = [
                'valueInputOption' => 'RAW'
            ];

            // Clear existing data and set headers
            $this->service->spreadsheets_values->clear($this->spreadsheetId, 'Sheet1', new ClearValuesRequest());

            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                'Sheet1!A1:H1',
                $body,
                ['valueInputOption' => 'RAW']
            );

            $this->formatHeader();
            $this->autoResizeColumns();

            Log::info('Google Sheet initialized with headers');
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Sheet', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get all transactions from Google Sheets (for backup/sync purposes)
     */
    public function getAllTransactions()
    {
        try {
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, 'Sheet1!A2:H');
            return $response->getValues() ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to get transactions from Google Sheets', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
