<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MApiKey;
use PDF;
use Response;

class CADownload extends Controller
{
    public function download_invoice(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $kode_booking = $request->kode_booking;
        // $html = view("pdf.invoice")
        // ->with('kode_booking',$kode_booking);
        $pdf = PDF::loadView('pdf.invoice', ['kode_booking'=>$kode_booking]);

        $my_pdf_path_for_example = 'download/invoice/';
        if (!file_exists(public_path($my_pdf_path_for_example))) {
            mkdir(public_path($my_pdf_path_for_example), 0777, true);
            $path =time().rand(1,100).'-invoice.pdf';
            $pdf->save($my_pdf_path_for_example.$path);
        }else{
            $path =time().rand(1,100).'-invoice.pdf';
            $pdf->save($my_pdf_path_for_example.$path);
        }        
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'url' => url($my_pdf_path_for_example.$path),
        ], 200);        
    }

    public function download_trip_detail(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $kode_booking = $request->kode_booking;        
        $pdf = PDF::loadView('pdf.tripDetail', ['kode_booking'=>$kode_booking]);

        $my_pdf_path_for_example = 'download/trip_detail/';
        if (!file_exists(public_path($my_pdf_path_for_example))) {
            mkdir(public_path($my_pdf_path_for_example), 0777, true);
            $path = time().rand(1,100).'-trip_detail.pdf';
            $pdf->save($my_pdf_path_for_example.$path);
        }else{
            $path = time().rand(1,100).'-trip_detail.pdf';
            $pdf->save($my_pdf_path_for_example.$path);
        }        
        
        // $headers = array(
        //     'Content-Type: application/pdf',
        // );

        // return Response::download($path, 'trip_detail.pdf', $headers);;
        
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'url' => url($my_pdf_path_for_example.$path),
        ], 200);        
    }
}
