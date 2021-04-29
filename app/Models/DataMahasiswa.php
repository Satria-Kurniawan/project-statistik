<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataMahasiswa extends Model
{
    // public function dataMahasiswa()
    // {
    //     return [
    //         [
    //             'No' => '1',
    //             'Nama' => 'Satria',
    //             'Nilai' => 'Nilai'
    //         ]
    //     ];
    // }
    protected $table = 'tabel_mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    protected $fillable = [
        'nama_mahasiswa', 'nilai_mahasiswa',
    ];

    public function dataMahasiswa(){
        return DB::table('tabel_mahasiswa')->get();
    }

    public function addData($data){
        DB::table('tabel_mahasiswa')->insert($data);
    }

    public function editData($id_mahasiswa, $data){
        DB::table('tabel_mahasiswa')
        ->where('id_mahasiswa',$id_mahasiswa)
        ->update($data);
    }
}
