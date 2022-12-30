<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HPesan;
use App\Models\HPesanDetail;
use Auth;
use App\Services\Firestore;
use Google\Cloud\Firestore\DocumentReference;
use Carbon\Carbon;

class CChat extends Controller
{
    public function index()
    {        
        $pesan = HPesan::orderBy('waktu_pesan_terakhir','desc')->get();
        return view('chat.index_baru')
            ->with('pesan',$pesan)
            ->with('title','Chat');
    }

    public function chat_detail($id)
    {        
        // dd($id);
        $pesan = HPesanDetail::join('m_users','m_users.id_user','h_pesan_detail.id_user')
            ->join('m_customer','m_customer.id','m_users.id_ref')
            ->select('h_pesan_detail.*','m_customer.nama_depan','m_customer.nama_belakang')
            ->where('h_pesan_detail.id_pesan',$id)
            ->orderBy('h_pesan_detail.created_date','asc')
            ->get();
        // dd($pesan);
        return response()->json(['data'=>$pesan]);
    }
    public function tambah_chat_detail(Request $request)
    {        
        // dd($id);
        $detail = HPesanDetail::where('id_pesan',$request->id_pesan)->latest()->first();

        $pesan = new HPesanDetail();
        $pesan->id_pesan = $request->id_pesan;
        $pesan->id_ref = $detail->id_ref;
        $pesan->pesan = $request->pesan;
        $pesan->id_user = Auth::user()->id_user;
        $pesan->id_tipe = 3;
        $pesan->save();

        $p = HPesan::find($request->id_pesan);
        $p->pesan_terakhir = $request->pesan;
        $p->waktu_pesan_terakhir = Carbon::now();
        $p->update();

        $firestore = Firestore::get();

        $fireDetail = $firestore->collection('h_pesan_detail')->newDocument();
        $fireDetail->set([    
            'id_pesan_detail' => $pesan->id_pesan_detail,
            'id_pesan' =>  intval($pesan->id_pesan),
            'id_ref' => $pesan->id_ref,
            'id_tipe' => $pesan->id_tipe,
            'url' => "",
            'pesan' => $pesan->pesan,
            'created_date' => date('Y-m-d H:i:s'),
            'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
            'id_user' => Auth::user()->id_user,
        ]);

        $query = $firestore->collection('h_pesan')
            ->where('id_pesan', '=', intval($pesan->id_pesan));
        
        $documents = $query->documents();        
        $id = null;
        foreach ($documents as $document) {
            $id = $document->id();
            $doc = $firestore->collection('h_pesan')->document($id)
                ->set([
                    'badge' => $document['badge'],
                    'created_date' => $document['created_date'],
                    'id_pesan' => $document['id_pesan'],
                    'id_ref' => $document['id_ref'],
                    'id_user_penerima' => $document['id_user_penerima'],
                    'id_user_pengirim' => $document['id_user_pengirim'],
                    'judul' => $document['judul'],
                    'penerima_lihat' => $document['penerima_lihat'],
                    'pengirim_lihat' => $document['pengirim_lihat'],
                    'pesan_terakhir' => $pesan->pesan,
                    'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
                    'waktu_pesan_terakhir' => date('Y-m-d H:i:s')
                ]);
        }

        $data = HPesanDetail::join('m_users','m_users.id_user','h_pesan_detail.id_user')
            ->join('m_customer','m_customer.id','m_users.id_ref')
            ->select('h_pesan_detail.*','m_customer.nama_depan','m_customer.nama_belakang')
            ->where('h_pesan_detail.id_pesan_detail',$pesan->id_pesan_detail)->first();
        // dd($data);

        return response()->json(['status'=>true,'data'=>$data]);
    }
}
