<?php


namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    // Chart of Accounts
    public function chartOfAccounts()
    {
        return view('backend.accounting.chart_of_accounts');
    }

    // Ledgers
    public function ledgers()
    {
        return view('backend.accounting.ledgers');
    }

    // Journal Entries
    public function journalEntries()
    {
        return view('backend.accounting.journal_entries');
    }

    // Bank Accounts
    public function bankAccounts()
    {
        return view('backend.accounting.bank_accounts');
    }

    // Wallets
    public function wallets()
    {
        return view('backend.accounting.wallets');
    }

    // Receipts & Payments
    public function receiptsPayments()
    {
        return view('backend.accounting.receipts_payments');
    }

    // Reconciliation
    public function reconcile()
    {
        return view('backend.accounting.reconcile');
    }

    // Reports
    public function trialBalance()
    {
        return view('backend.accounting.trial_balance');
    }

    public function incomeStatement()
    {
        return view('backend.accounting.income_statement');
    }

    public function balanceSheet()
    {
        return view('backend.accounting.balance_sheet');
    }

    public function taxReports()
    {
        return view('backend.accounting.tax_reports');
    }
}
