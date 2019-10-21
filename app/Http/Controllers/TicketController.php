<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\QRCode;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    
    public function index()
    {
        return response()->json(Ticket::with('qr_codes')->get(), 200);
    }

    
    public function store(Request $request)
    {
        $ticket = null;
        DB::transaction(function () use ($request, &$ticket){
            $ticket = Ticket::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'price' => $request->input('price'),
                'quantity' => $request->input('quantity'),
                'type_id' => $request->input('type_id'),
                'product_id' => $request->input('product_id'),
            ]);

            //Generate QR
            $qrcode = QRCode::create([
                'qr_code' => $ticket->id . "_" . $ticket->name . "_" . $ticket->quantity . "_" . $ticket->is_vvip. "_" . $ticket->is_vip,
                'ticket_id' => $ticket->id
            ]);

            $product = Product::find($ticket['product_id']);
            $product->stock -= $ticket['quantity'];
            $product->save();
        },3);
        return response()->json($ticket, 201);
    }

    
    public function show($email)
    {
        return response()->json(Ticket::with('qr_codes')->where('email', $email)->get(), 200);
    }

    
    public function update(Request $request, Ticket $ticket)
    {
        $status = $ticket->update(
            $request->only([
                'name',
                'email',
                'price',
                // 'type',
            ])
        );
        return response()->json([
            'status' => $status,
            'message' => $status ? 'Harga Tiket Diupdate' : 'Gagal Mengganti harga Tiket'
        ],200);
    }

    public function redeem(Request $request, $qrcode)
    {
        $status = QRCode::where('qr_code', "LIKE", $qrcode)->first();
        if(!$status){
                return response()->json('Invalid Ticket');
            }
        else if($status->is_checkin != true){
                $data = QRCode::where('qr_code', "LIKE", $qrcode)
                        ->update(['is_checkin' => true ]);
                $datauser = QRCode::where('qr_code', "LIKE", $qrcode)->with(['tickets'])->first();
                return response()->json([
                        'message' => $data ? 'Check-In Success!' : 'Error Check-In',
                        'userdata' => $datauser
                    ]);
                }
        else{
                return response()->json(['Ticket Already Redeemed']);
            }
    }

    public function paid(Ticket $ticket)
    {
        $ticket->is_paid = true;
        $status = $ticket->save();

        return response()->json([
            'Status' => $status,
            'Message' => $status ? 'Status Pembayaran Berhasil di Ganti' : 'Gagal Mengganti Status Pembayaran'
        ]);
    }

    public function uploadBuktiTF(Ticket $ticket)
    {
        $status = Ticket::find($ticket);
        $status->image= $this->uploadImage($request);
        $status->update();
        return response()->json([
            'Status' => $status,
            'Message' => $status ? 'Berhasil Upload Bukti Pembayaran' : 'Gagal Upload Bukti Pembayaran'
        ]);
    }

    public function uploadImage(Request $request, $name = null)
    {
        if($request->hasFile('image')){
            $image = $request->file('image');
            if (is_null($name)) {
                $name = time() . "_" . rand(1000, 1000000) . "." . $image->getClientOriginalExtension();
            }
            $image->move(public_path('buktiTF'), $name);
            return '/buktiTF/'.$name;
        }
        return '';
    }

    public function destroy(Ticket $ticket)
    {
        $status = DB::transaction(function () use ($ticket)
        {
            $product = Product::find($ticket['product_id']);
            $product->stock += $ticket['quantity'];
            $product->save();
            $ticket->qr_codes()->delete();
            $ticket->delete();
        },3);
         
        return response()->json([
            'status' => (bool)$ticket,
            'message' => $ticket ? "Berhasil Menghapus Ticket" : "Gagal Menghapus Ticket"
        ]);
    }
}
