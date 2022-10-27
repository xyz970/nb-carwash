<?php

namespace App\Http\Controllers;

use App\Models\WashType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RateController extends Controller
{
    public function index(Request $request)
    {
        $washType = WashType::all();
        if ($request->ajax()) {
            return DataTables::of($washType)
            ->addColumn('type', function ($item) {
                // '.route("crew.changeStatus",$item->id).
                if ($item->type == "Mobil") {
                    $label = '<span class="badge bg-info">Mobil</span>';
                }elseif ($item->type == "Motor") {
                    $label = '<span class="badge bg-success">Motor</span>';
                }else{
                    $label = '<span class="badge bg-secondary"> Karpet</span>';
                }
                return $label;
            })
            ->addColumn('action', function ($item) {
                // '.route("crew.changeStatus",$item->id).
                $btn = '
    <button class="btn btn-primary" onclick="updateRate(`' . $item->id . '`)" >
        Edit
    </button></div>';
                return $btn;
            })
            ->addIndexColumn()
            ->rawColumns([ 'action','type'])
            ->make(true);
        }
        return view('admin.tarif');
    }
    public function edit(Request $request, $id)
    {
        $rate = WashType::find($id);
        if ($request->ajax()) {
            return response()->json($rate);
        }

    }
    public function update(Request $request,$id)
    {
        $rate = WashType::find($id);
        
        try {
            $rate->price = $request->input('price');
            $rate->update();
            return response()->json(['status'=>'success']);
        } catch (\Throwable $th) {
            return response()->json(['status'=>'fail','message'=>$th->getTraceAsString()]);
        }
    }
}
