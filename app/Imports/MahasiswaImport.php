<?php

namespace App\Imports;

use App\Models\DataMahasiswa;
use Maatwebsite\Excel\Concerns\ToModel;

class MahasiswaImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new DataMahasiswa([
            'id_mahasiswa' => $row[0],
            'nilai_mahasiswa' => $row[2],
        ]);
    }
}
