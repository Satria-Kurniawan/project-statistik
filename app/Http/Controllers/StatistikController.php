<?php

namespace App\Http\Controllers;

use App\Exports\MahasiswaExport;
use App\Imports\MahasiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\DataMahasiswa;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function __construct()
    {
        $this->DataMahasiswa = new DataMahasiswa();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('cari')){
            $data = DataMahasiswa::where('nilai_mahasiswa','LIKE','%'.$request->cari."%")->get();
            $frekuensi = DataMahasiswa::select('nilai_mahasiswa', DB::raw('count(*) as frekuensi'))->groupBy('nilai_mahasiswa')->where('nilai_mahasiswa','LIKE','%'.$request->cari."%")->get();
            $totalfrekuensi = $data->count('nilai_mahasiswa');
            $totalskor = $data->sum('nilai_mahasiswa');
            $maxSkor = $data->max('nilai_mahasiswa');
            $minSkor = $data->min('nilai_mahasiswa');
            $rata2 = $data->average('nilai_mahasiswa');
        }else{
            $data = DataMahasiswa::all();
            $frekuensi = DataMahasiswa::select('nilai_mahasiswa', DB::raw('count(*) as frekuensi'))
                                ->groupBy('nilai_mahasiswa')
                                ->get();
            $totalfrekuensi = DataMahasiswa::count('nilai_mahasiswa');
            $totalskor = DataMahasiswa::sum('nilai_mahasiswa');
            $maxSkor = DataMahasiswa::max('nilai_mahasiswa');
            $minSkor = DataMahasiswa::min('nilai_mahasiswa');
            $rata2 = number_format(DataMahasiswa::average('nilai_mahasiswa'),3);
        }

        // $data = DataMahasiswa::all();
    //    $maxSkor = DataMahasiswa::max('nilai_mahasiswa');
    //    $minSkor = DataMahasiswa::min('nilai_mahasiswa');
    //    $rata2 = number_format(DataMahasiswa::average('nilai_mahasiswa'),3);


       //untuk tabel frekuensi
    //    $frekuensi = DataMahasiswa::select('nilai_mahasiswa', DB::raw('count(*) as frekuensi'))  //ambil skor, hitung banyak skor taruh di tabel frekuensi
    //                             ->groupBy('nilai_mahasiswa')                              //urutkan sesuai skor
    //                             ->get();
    //    $totalskor = DataMahasiswa::sum('nilai_mahasiswa');
    //    $totalfrekuensi = DataMahasiswa::count('nilai_mahasiswa');        //karena total frekuensi = banyaknya skor yang ada

       return view('/statistik/index', ['mahasiswa' => $data,
                            'max' => $maxSkor,
                            'min' => $minSkor,
                            'rata2' => $rata2,
                            'frekuensi' => $frekuensi,
                            'totalskor' => $totalskor,
                            'totalfrekuensi' => $totalfrekuensi]);    //tampilkan home.blade
        $data = [
            'mahasiswa' => $this->DataMahasiswa->dataMahasiswa(),
        ];
        // return view('statistik/index', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = [
            'id_mahasiswa' => Request()->id,
            'nama_mahasiswa' => Request()->nama,
            'nilai_mahasiswa' => Request()->nilai,
        ];

        $this->DataMahasiswa->addData($data);
        return redirect()->route('mahasiswa')->with('pesan', 'Berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_mahasiswa)
    {
        $data = DataMahasiswa::find($id_mahasiswa);

        if(!$data){
            abort(404);
        }

        return view('statistik/edit', ['mahasiswa' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_mahasiswa)
    {
        $data = [
            'nama_mahasiswa' => Request()->nama,
            'nilai_mahasiswa' => Request()->nilai,
        ];

        $this->DataMahasiswa->editData($id_mahasiswa, $data);
        return redirect()->route('mahasiswa')->with('pesan', 'Berhasil diedit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_mahasiswa)
    {
        return $data;
    }

    public function delete($id_mahasiswa)
    {
       $data = DataMahasiswa::find($id_mahasiswa);
       $data->delete();

       return redirect('/statistik')->with('pesan', 'Berhasil dihapus');
    }

    public function mahasiswaExport(){
        return Excel::download(new MahasiswaExport, 'nilai.xlsx');
    }

    public function mahasiswaImport(Request $request){
        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move('DataNilai', $namaFile);

        Excel::import(new MahasiswaImport, public_path('/DataNilai/'.$namaFile));

        return redirect('/statistik');
    }
}
