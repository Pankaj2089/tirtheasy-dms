<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\OrderPayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Carbon;

class GuestDatabaseController extends Controller
{
    private static $HotelsModel;
    private static $OrdersModel;
    private static $OrderPaymentsModel;

    public function __construct()
    {
        self::$HotelsModel = new Hotels();
        self::$OrdersModel = new Orders();
        self::$OrderPaymentsModel = new OrderPayments();
    }

    public function index(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return redirect('/');
        }

        $vendorId = $request->session()->get('vendor_id');

        // Get guest data for the logged in vendor
        $guests = $this->getGuestsForVendor($vendorId);

        $siteUrl = env('APP_URL');

        return view('guest-database.index', compact('guests', 'siteUrl'));
    }

    public function export(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return redirect('/');
        }

        $vendorId = $request->session()->get('vendor_id');

        // Get guest data for export
        $guests = $this->getGuestsForVendor($vendorId);

        // Generate Excel file
        return $this->exportToExcel($guests);
    }

    private function getGuestsForVendor($vendorId)
    {
        $orders = self::$OrdersModel
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where(function ($q) {
                $q->whereNull('orders.is_cancelled')
                    ->orWhere('orders.is_cancelled', '!=', 1);
            })
            ->select([
                'orders.id',
                'orders.billing_name',
                'orders.billing_email',
                'orders.billing_phone',
                'orders.check_in_date',
                'orders.check_out_date',
                'orders.rooms',
                'orders.adults',
                'orders.childs',
                'orders.invoice_id',
                'orders.payment_status',
                'orders.grand_total',
                'orders.other_details',
                'hotel.title as hotel_name'
            ])
            ->orderBy('orders.created_at', 'desc')
            ->get();

        $guests = [];
        foreach ($orders as $order) {
            $details = [];
            if (!empty($order->other_details)) {
                $decoded = json_decode($order->other_details, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $details = $decoded;
                }
            }

            // Determine source (Tirtheasy or Offline)
            $source = isset($details['userDetails']) ? 'Tirtheasy' : 'Offline';

            $guests[] = [
                'id' => $order->id,
                'guest_name' => $order->billing_name ?? 'Guest',
                'phone' => $order->billing_phone ?? '',
                'email' => $order->billing_email ?? '',
                'source' => $source,
                'rooms' => $order->rooms ?? 1,
                'check_in_date' => $order->check_in_date,
                'check_out_date' => $order->check_out_date,
                'hotel_name' => $order->hotel_name,
                'invoice_id' => $order->invoice_id,
                'payment_status' => $order->payment_status,
                'total_amount' => $order->grand_total
            ];
        }

        return $guests;
    }

    private function exportToExcel($guests)
    {
        $filename = 'guest_database_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($guests) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Guest Name',
                'Phone',
                'Email',
                'Source',
                'Rooms',
                'Check-in Date',
                'Check-out Date',
                'Hotel',
                'Invoice ID',
                'Payment Status',
                'Total Amount'
            ]);

            // Add data rows
            foreach ($guests as $guest) {
                fputcsv($file, [
                    $guest['guest_name'],
                    $guest['phone'],
                    $guest['email'],
                    $guest['source'],
                    $guest['rooms'],
                    $guest['check_in_date'],
                    $guest['check_out_date'],
                    $guest['hotel_name'],
                    $guest['invoice_id'],
                    $guest['payment_status'],
                    $guest['total_amount']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
