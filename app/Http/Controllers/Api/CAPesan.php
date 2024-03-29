<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HPesan;
use App\Models\HPesanDetail;
use App\Models\MApiKey;
use App\Models\User;
use App\Models\TokenFcm;
use App\Services\Firestore;
use Google\Cloud\Firestore\DocumentReference;

class CAPesan extends Controller
{
    public function get_chat(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        // $muser = User::where('id_user',$user->id_user)->first();
        $id_user = $request->id_user;

        $tipe = HPesan::from('h_pesan as a')
                ->selectRaw('a.*, b.nama_depan as nama_depan_pengirim, b.nama_belakang as nama_belakang_pengirim, c.nama_depan as nama_depan_penerima, c.nama_belakang as nama_belakang_penerima')
                ->leftJoin('m_users as d','d.id_user', '=','a.id_user_pengirim')
                ->leftJoin('m_customer as b','b.id', '=','d.id_ref')
                ->leftJoin('m_users as e','e.id_user', '=','a.id_user_penerima')
                ->leftJoin('m_customer as c','c.id', '=','e.id_ref')
                ->where('a.id_user_pengirim',$id_user)
                ->OrWhere('a.id_user_penerima',$id_user)
                ->orderBy('a.waktu_pesan_terakhir','desc')
                ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $tipe,
        ], 200);        
    }

    public function get_chat_detail(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        // $muser = User::where('id_user',$user->id_user)->first();
        $id_pesan = intval($request->id_pesan);

        $tipe = HPesanDetail::from('h_pesan_detail as a')
                ->selectRaw('a.*, b.nama_depan as nama_depan_pengirim, b.nama_belakang as nama_belakang_pengirim')
                ->leftJoin('m_users as d','d.id_user', '=','a.id_user')
                ->leftJoin('m_customer as b','b.id', '=','d.id_ref')                
                ->where('a.id_pesan',$id_pesan)
                ->orderBy('a.created_date','desc')
                ->get();

		$p = HPesan::find($id_pesan);
		$p->pengirim_lihat = 0;
		$p->update();

		$firestore = Firestore::get();
		$query = $firestore->collection('h_pesan')
				->where('id_pesan', '=', $id_pesan);
			
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
					'id_booking' => $document['id_booking'],
                    'judul_mobile' => $document['judul_mobile'],
					'penerima_lihat' => $document['penerima_lihat'],
					'pengirim_lihat' => 0,
					'pesan_terakhir' => $document['pesan_terakhir'],
					'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
					'waktu_pesan_terakhir' => $document['waktu_pesan_terakhir']
				]);
		}
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $tipe,
        ], 200);        
    }

    public function insert_chat_detail(Request $request)
	{                
		$user = MApiKey::where('token',$request->header('auth-key'))->first();
			// $muser = User::where('id_user',$user->id_user)->first();

			$id_pesan = intval($request->id_pesan);
			$id_ref = $request->id_ref;
			$id_tipe = $request->id_tipe;
			$pesan = $request->pesan;

			$hdetail = new HPesanDetail();
			$hdetail->id_pesan = $id_pesan;
			$hdetail->id_ref = $id_ref;
			$hdetail->id_tipe = $id_tipe;
			$hdetail->pesan = $pesan;
			$hdetail->id_user = $user->id_user;
			$hdetail->save();

			$hpesan = HPesan::find($id_pesan);
			$hpesan->pesan_terakhir = $pesan;
			$hpesan->waktu_pesan_terakhir = date('Y-m-d H:i:s');
			$hpesan->penerima_lihat = 0;
			$hpesan->update();

			$firestore = Firestore::get();

			$fireDetail = $firestore->collection('h_pesan_detail')->newDocument();
			$fireDetail->set([    
				'id_pesan_detail' => $hdetail->id_pesan_detail,
				'id_pesan' => $id_pesan,
				'id_ref' => $id_ref,
				'id_tipe' => $id_tipe,
				'url' => "",
				'pesan' => $pesan,
				'created_date' => date('Y-m-d H:i:s'),
				'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
				'id_user' => $user->id_user,
			]);

			$query = $firestore->collection('h_pesan')
				->where('id_pesan', '=', $id_pesan);
			
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
						'id_booking' => $document['id_booking'],
                        'judul_mobile' => $document['judul_mobile'],
						'penerima_lihat' => 0,
						'pengirim_lihat' => $document['pengirim_lihat'],
						'pesan_terakhir' => $pesan,
						'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
						'waktu_pesan_terakhir' => date('Y-m-d H:i:s')
					]);
			}
				// $docu = $firestore->collection('h_pesan')->document($id);
				// $data = $docu->get();     
				// $data = $document->get();
			// $doc = $firestore->collection('h_pesan')->document($id);
			// $doc->update([   
			//     ['badge' => $data['badge']],
			//     ['created_date' => $data['created_date']],
			//     ['id_pesan' => $data['id_pesan']],
			//     ['id_ref' => $data['id_ref']],
			//     ['id_user_penerima' => $data['id_user_penerima']],
			//     ['id_user_pengirim' => $data['id_user_pengirim']],
			//     ['judul' => $data['judul']],
			//     ['penerima_lihat' => $data['penerima_lihat']],
			//     ['pengirim_lihat' => $data['pengirim_lihat']],
			//     ['pesan_terakhir' => $pesan],
			//     ['updatedDate' => $data['updatedDate']],
			//     ['waktu_pesan_terakhir' => date('Y-m-d H:i:s')]
			// ]);

		return response()->json([
			'success' => true,
			'message' => 'Success',
			'code' => 1,            
		], 200);        
	}
	public function update_pengirim_lihat(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        // $muser = User::where('id_user',$user->id_user)->first();
        $id_pesan = intval($request->id_pesan);
				
		$p = HPesan::find($id_pesan);
		$p->pengirim_lihat = 0;
		$p->update();

		$firestore = Firestore::get();
		$query = $firestore->collection('h_pesan')
				->where('id_pesan', '=', $id_pesan);
			
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
					'id_booking' => $document['id_booking'],
                    'judul_mobile' => $document['judul_mobile'],
					'penerima_lihat' => $document['penerima_lihat'],
					'pengirim_lihat' => 0,
					'pesan_terakhir' => $document['pesan_terakhir'],
					'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
					'waktu_pesan_terakhir' => $document['waktu_pesan_terakhir']
				]);
		}
        
        return response()->json([
            'success' => true,
            'message' => 'Success Update Pengirim lihat',
            'code' => 1,            
        ], 200);        
    }

	public function update_token_fcm(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $token_fcm = $request->token_fcm;
        $token = $request->token;

		$fcm = TokenFcm::where('id_user',$user->id_user)->first();
		if ($fcm) {
			$tFcm = TokenFcm::find($fcm->id);
			$tFcm->fcm_token = $token_fcm;
			$tFcm->token = $token;
			$tFcm->update();
		}else{
			$tFcm = new TokenFcm();
			$tFcm->id_user = $user->id_user;
			$tFcm->fcm_token = $token_fcm;
			$tFcm->token = $token;
			$tFcm->save();
		}
        
        return response()->json([
            'success' => true,
            'message' => 'Success update fcm',
            'code' => 1,
        ], 200);        
    }

	public function post_chat_upload_document(Request $request)
	{                
		$user = MApiKey::where('token',$request->header('auth-key'))->first();			

			$id_pesan = intval($request->id_pesan);
			$id_ref = $request->id_ref;
			$id_tipe = 4;
			$pesan = 'upload document';
			$name = '';

			$my_pdf_path_for_example = 'upload/chat/';
			if (!file_exists(public_path($my_pdf_path_for_example))) {
				mkdir(public_path($my_pdf_path_for_example), 0777, true);
			}

			if ($request->file('file')) {            

				$file = $request->file('file');
				$name = round(microtime(true) * 1000).'.'.$file->extension();
				$extension = $file->extension();
                $file->move(public_path('upload/chat/'), $name);
				
				if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif') {
					$this->compress(public_path('upload/chat/'.$name),public_path('upload/chat/'.$name));
				}
			}else{
				return response()->json([
					'success' => false,
					'message' => 'error no files',
					'code' => 0,            
				], 400);
			}

			$hdetail = new HPesanDetail();
			$hdetail->id_pesan = $id_pesan;
			$hdetail->id_ref = $id_ref;
			$hdetail->id_tipe = $id_tipe;
			$hdetail->pesan = $pesan;
			$hdetail->id_user = $user->id_user;
			$hdetail->url = $name;
			$hdetail->save();

			$hpesan = HPesan::find($id_pesan);
			$hpesan->pesan_terakhir = $pesan;
			$hpesan->waktu_pesan_terakhir = date('Y-m-d H:i:s');
			$hpesan->penerima_lihat = 0;
			$hpesan->update();

			$firestore = Firestore::get();

			$fireDetail = $firestore->collection('h_pesan_detail')->newDocument();
			$fireDetail->set([    
				'id_pesan_detail' => $hdetail->id_pesan_detail,
				'id_pesan' => $id_pesan,
				'id_ref' => $id_ref,
				'id_tipe' => $id_tipe,
				'url' => $name,
				'pesan' => $pesan,
				'created_date' => date('Y-m-d H:i:s'),
				'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
				'id_user' => $user->id_user,
			]);

			$query = $firestore->collection('h_pesan')
				->where('id_pesan', '=', $id_pesan);
			
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
						'id_booking' => $document['id_booking'],
                        'judul_mobile' => $document['judul_mobile'],
						'penerima_lihat' => 0,
						'pengirim_lihat' => $document['pengirim_lihat'],
						'pesan_terakhir' => $pesan,
						'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
						'waktu_pesan_terakhir' => date('Y-m-d H:i:s')
					]);
			}
				
		return response()->json([
			'success' => true,
			'message' => 'Success',
			'code' => 1,            
		], 200);        
	}

	public function download_upload_document(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $id_pesan = $request->id_pesan;
        $path = $request->nama_file;        
        $my_pdf_path_for_example = 'upload/chat/';        
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'url' => url($my_pdf_path_for_example.$path),
        ], 200);        
    }

	public function compress($source, $destination, $quality = 40) {
		// dd($source);
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
          $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
          $image = imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
          $image = imagecreatefrompng($source);
        }
        imagejpeg($image, $destination, $quality);
        return $destination;
    }
}
