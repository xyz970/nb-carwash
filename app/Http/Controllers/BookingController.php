<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // $washType =
            $booking = Booking::with('washType')->where('is_valid', '=', 'true')->get();
        if ($request->ajax()) {
            return DataTables::of($booking)
                // ->addColumn('status', function ($item) {
                //     if ($item->time == 'true')
                //         $keterangan = '<td><span class="badge rounded-pill badge-success">Pesanan Selesai</span></td>';
                //     else
                //         $keterangan = '<td>
                //             <span class="badge rounded-pill badge-secondary">Proses <i class="fa fa-spin fa-circle-o-notch"></i></span>
                //             </td>';
                //     return $keterangan;
                // })
                ->addColumn('type', function ($item) {
                    // '.route("crew.changeStatus",$item->id).
                    if ($item->washType->type == "Mobil") {
                        $label = '<span class="badge bg-info">Mobil</span>';
                    } elseif ($item->washType->type == "Motor") {
                        $label = '<span class="badge bg-success">Motor</span>';
                    } else {
                        $label = '<span class="badge bg-secondary"> Karpet</span>';
                    }
                    return $label;
                })
                ->addColumn('action', function ($item) {
                    // '.route("crew.changeStatus",$item->id).
                    $btn = '
        <a class="btn btn-danger" href='.route("admin.booking.done",$item->name).'  style="color: white;">
            Verifikasi
        </a></div>';
                    return $btn;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'type'])
                ->make(true);
        }
        return view('admin.booking',);
    }

    public function costumerBook(Request $request)
    {
        if ($request->ajax()) {
            $count = Booking::where('is_valid', '=', 'false')->count();
            return response()->json(['count' => $count]);
        }
        return view('booking');
    }

    public function insert(Request $request)
    {
        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i');
        $id = $date . '-' . Str::snake(Str::lower($request->input('name'))) . '-' . $time;
        $input = $request->only('name', 'time', 'wash_type_id', 'merk_model', 'plate_number', 'total');
        $secInput = array(
            'id' => $id,
            'date' => $date,
        );
        $array = $input + $secInput;
        Booking::create($array);
        // dd($input,$request->input('times'));
        return redirect()->to("booking/success?time=" . $input['time']);
    }

    public function success(Request $request)
    {
        // if ($request->input('time')) {
        //     dd($request->input('time'));
        // }
        return view('booking-success');
    }

    public function verification()
    {
        return view('booking-verification');
    }

    public function verification_check(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->only('name');
            $booking  = Booking::where('name', '=', $input['name'])->first();
            $check_valid  = Booking::where('name', '=', $input['name'])->where('is_valid', '=', 'true')->first();
            if (!$booking) {
                return response()->json(['message' => 'Data reservasi tidak ditemukan'], 404);
            } elseif ($check_valid) {
                return response()->json(['message' => 'Data reservasi telah diverifikasi'], 406);
            } else {
                $booking->is_valid = 'true';
                $booking->update();
                // Transaction::create($field);
                return response()->json(['message' => 'Data reservasi berhasil divalidasi']);
            }
        }
    }

    public function done($name)
    {
        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i');
       
        $booking  = Booking::where('name', '=', $name)->first();
        $id = $date . '-' . Str::snake(Str::lower($booking->name)) . '-' . $time;
        $field = array(
            'id' => $id,
            'name' => $booking->name,
            'plate_number' => $booking->plate_number,
            'merk_model' => $booking->merk_model,
            'wash_type_id' => $booking->wash_type_id,
            'time' => $booking->time,
            'total' => $booking->total,
            'date' => $booking->date,
        );
        Transaction::create($field);
        $booking->delete();
        return redirect()->back();
        // dd($id);
    }
}
