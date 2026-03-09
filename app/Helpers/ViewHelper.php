<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

/**
 * Helper untuk menentukan view path berdasarkan role user
 */
class ViewHelper
{
    /**
     * Get view name berdasarkan role, dengan fallback ke attendant view
     * 
     * @param string $viewName Nama view tanpa prefix (e.g., 'transaction.receipt')
     * @return string Full view path
     */
    public static function getRoleBasedView(string $viewName): string
    {
        $role = Auth::user()->role ?? 'attendant';
        
        // View hierarchy: role-specific > attendant (default)
        $roledView = "{$role}.{$viewName}";
        
        if (view()->exists($roledView)) {
            return $roledView;
        }
        
        // Fallback to attendant view
        return "attendant.{$viewName}";
    }

    /**
     * Get view path untuk transaction receipts
     */
    public static function getTransactionReceiptView(string $type = 'receipt'): string
    {
        // $type: 'receipt' atau 'entry_receipt'
        return self::getRoleBasedView("transaction.{$type}");
    }
}
