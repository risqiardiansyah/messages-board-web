<?php

namespace App\Http\Repositories;

use App\Http\Resources\DapilResource;
use App\Models\Pesan;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MainRepository
{
  public function getAllPesan($request)
  {
    $limit = $request->limit;
    $offset = $request->offset;
    $search = $request->search;

    try {
      $select = [
        'pesan.*',
        'u.nama',
        'u.email'
      ];
      $data = Pesan::select($select)
        ->leftJoin('user as u', 'u.id', '=', 'pesan.id_user')
        ->orderBy('pesan.created_at', 'DESC');
      if (isset($search)) {
        $data = $data->where('u.nama', 'LIKE', '%' . $search . '%')
          ->orWhere('u.email', 'LIKE', '%' . $search . '%')
          ->orWhere('isi', 'LIKE', '%' . $search . '%');
      }
      $data = $data->skip($offset)->take($limit)->get();

      return ['success' => true, 'data' => $data];
    } catch (\Exception $e) {
      return ['success' => false, 'msg' => $e->getMessage()];
    }
  }

  public function createPesan($request)
  {
    try {
      $email = $request->email;
      $nama = $request->nama;
      $id = checkEmailExists($email, $nama);

      $data = [
        'unique_code' => generateFiledCode('UNI'),
        'id_user' => $id,
        'isi' => $request->isi
      ];
      Pesan::insert($data);

      return ['success' => true, 'data' => []];
    } catch (\Exception $e) {
      return ['success' => false, 'msg' => $e->getMessage()];
    }
  }

  public function updatePesan($request)
  {
    $email = $request->email;
    $nama = $request->nama;
    $id = checkEmailExists($email, $nama);

    try {
      $data = [
        'isi' => $request->isi,
        'id_user' => $id,
      ];
      Pesan::where('id', $request->id)->update($data);

      return ['success' => true, 'data' => [
        'id' => $id,
        'nama' => $nama,
        'email' => $email,
        'isi' => $request->isi,
        'user' => User::where('id', $id)->first(),
      ]];
    } catch (\Exception $e) {
      return ['success' => false, 'msg' => $e->getMessage()];
    }
  }

  public function deletePesan($id)
  {
    try {
      Pesan::where('id', $id)->delete();

      return ['success' => true, 'data' => []];
    } catch (\Exception $e) {
      return ['success' => false, 'msg' => $e->getMessage()];
    }
  }
}
