<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Orders;

class OrderController extends Controller{

    public function getNewBookings(Request $request)
    {
        if($request->session()->has('vendor_login_id')){
            $vendorId = $request->session()->get('vendor_id');
            $orders = Orders::where('orders.vendor_id', $vendorId)
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->join('rooms', 'orders.room_id', '=', 'rooms.id')
            ->where('orders.is_read', 0)
            ->where('orders.platform', 'Online')
            ->select([
                'orders.id',
                'orders.invoice_id',
                'orders.check_in_date',
                'orders.check_out_date',
                'orders.billing_name',
                'rooms.title as room_name',
                'hotel.title as hotel_name',
                'orders.created_at'
            ])
            ->latest()
            ->get();

            return response()->json([
                'status' => true,
                'data' => $orders
            ]);
        } else{
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);    
        }
    }

    public function markAsRead(Request $request)
    {
        Orders::whereIn('id', $request->ids)->update(['is_read' => 1]);
        return response()->json(['status' => true]);
    }
}
?>