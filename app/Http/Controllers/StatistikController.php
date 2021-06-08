<?php

namespace App\Http\Controllers;

use App\Exports\MahasiswaExport;
use App\Imports\MahasiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\DataMahasiswa;
use App\Models\ZTabel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Cloner\Data;

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
    //    $data = DataMahasiswa::find($id_mahasiswa);
    //    $data->delete();
        $this->DataMahasiswa->deleteData($id_mahasiswa);

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

    public function dataBergolong(){

        $maxSkor = DataMahasiswa::max('nilai_mahasiswa');
        $minSkor = DataMahasiswa::min('nilai_mahasiswa');
        $n = DataMahasiswa::count('nilai_mahasiswa');
        //mencari rentangan
        $rentangan = $maxSkor - $minSkor;

        //mencari kelas
        $kelas = ceil(1 + 3.3 * log10 ($n));

        //menghitung interval
        $interval = ceil($rentangan/$kelas);

        //set batas bawah dan batas atas
        $batasBawah = $minSkor;
        $batasAtas = 0;

        //data bergolong
        for($i = 0; $i < $kelas; $i++){
            $batasAtas = $batasBawah + $interval - 1;
            // $frekuensi[$i] = Anggota::where(function, $query){
            //     $query->select(DB::raw('SUM(frekuensi) as tabel1'))
            //             ->
            // }
            $frekuensi[$i] = DataMahasiswa::select(DB::raw('count(*) as frekuensi, nilai_mahasiswa'))
                                    ->where([
                                        ['nilai_mahasiswa', '>=', $batasBawah],
                                        ['nilai_mahasiswa', '<=', $batasAtas],
                                    ])
                                    ->groupBy()
                                    ->count();
            $data[$i] = $batasBawah. " - ". $batasAtas;
            $batasBawah = $batasAtas + 1;
        }


        return view ('statistik/data-bergolong', ['data' => $data,
                                        'frekuensi' => $frekuensi,
                                        'batasAtas' => $batasAtas,
                                        'batasBawah' => $batasBawah,
                                        'kelas' => $kelas,
                                        'interval' => $interval,
                                        'rentangan' => $rentangan,
                                        ]);
   }

   public function chiKuadrat(){

        $maxSkor = DataMahasiswa::max('nilai_mahasiswa');
        $minSkor = DataMahasiswa::min('nilai_mahasiswa');
        //$n = f0 = banyak skor/total frekuensi
        $n = DataMahasiswa::count('nilai_mahasiswa');
        $rata2 = number_format(DataMahasiswa::average('nilai_mahasiswa'), 2);

        //function standar deviasi
        function std_deviation($my_arr){
            $no_element = count($my_arr);
            $var = 0.0;
            $avg = array_sum($my_arr)/$no_element;
            foreach($my_arr as $i)
                {
                    $var += pow(($i - $avg), 2);
                }
            return (float)sqrt($var/$no_element);
        }

        //function desimal
        function desimal($nilai){
            if($nilai<0){
                $des = substr($nilai,0,4);
            } else {
                $des = substr($nilai,0,3);
            }
            return $des;
        }

        //function label
        function label($nilai){
            if($nilai<0){
                $str1 = substr($nilai,4,1);
            } else {
                $str1 = substr($nilai,3,1);
            }

            switch($str1){
                case '0':
                    $sLabel = 'nol';
                    break;
                case '1':
                    $sLabel = 'satu';
                    break;
                case '2':
                    $sLabel = 'dua';
                    break;
                case '3':
                    $sLabel = 'tiga';
                    break;
                case '4':
                    $sLabel = 'empat';
                    break;
                case '5':
                    $sLabel = 'lima';
                    break;
                case '6':
                    $sLabel = 'enam';
                    break;
                case '7':
                    $sLabel = 'tujuh';
                    break;
                case '8':
                    $sLabel = 'delapan';
                    break;
                case '9':
                    $sLabel = 'sembilan';
                    break;
                default: $sLabel = 'Tidak ada field';
            }

            return $sLabel;
        }

        //ambil nilai skor
        $anggota = DataMahasiswa::select('nilai_mahasiswa')->get();

        //masukin skor ke dalam array biar bsa dipakek sama functionnya
        $i = 0;
        foreach ($anggota as $a){
            $arraySkor[$i] = $a->nilai_mahasiswa;
            $i++;
        }

        //standar deviasi dari seluruh skor
        $SD = number_format(std_deviation($arraySkor), 2);

        //mencari rentangan
        $rentangan = $maxSkor - $minSkor;

        //mencari kelas
        $kelas = ceil(1 + 3.3 * log10 ($n));

        //menghitung interval
        $interval = ceil($rentangan/$kelas);

        //set batas bawah dan batas atas
        $batasBawah = $minSkor;
        $batasAtas = 0;

        //data chi
        $totalchi = 0;
        for($i = 0; $i < $kelas; $i++){
            //menghitung batas bawah
            $batasBawahBaru[$i] = $batasBawah - 0.5;

            $batasAtas = $batasBawah + $interval - 1;

            //menghitung batas atas
            $batasAtasBaru[$i] = $batasAtas + 0.5;

            //menghitung atas dan bawah z
            $zBawah[$i] = number_format(($batasBawahBaru[$i]- $rata2)/$SD, 2);
            $zAtas[$i] = number_format(($batasAtasBaru[$i]- $rata2)/$SD, 2);

            //menghitung z tabel atas dan bawah
            $cariDesimalBawah = desimal($zBawah[$i]);
            $cariDesimalAtas = desimal($zAtas[$i]);

            $labelDesimalBawah = label($zBawah[$i]);
            $labelDesimalAtas= label($zAtas[$i]);

            $zTabelBawah = ZTabel::where('z', '=', $cariDesimalBawah)->get();
            $zTabelAtas = ZTabel::where('z', '=', $cariDesimalAtas)->get();
            $zTabelBawahFix[$i] = $zTabelBawah[0]->$labelDesimalBawah;
            $zTabelAtasFix[$i] = $zTabelAtas[0]->$labelDesimalAtas;

            //menghitung l/proporsi
            $lprop[$i] = abs($zTabelBawahFix[$i] - $zTabelAtasFix[$i]);

            //menghitung fe(L*N)
            $fe[$i] = $lprop[$i]*$n;

            //menghitung f0
            $frekuensi[$i] = DataMahasiswa::select(DB::raw('count(*) as frekuensi, nilai_mahasiswa'))
                                    ->where([
                                        ['nilai_mahasiswa', '>=', $batasBawah],
                                        ['nilai_mahasiswa', '<=', $batasAtas],
                                    ])
                                    ->groupBy()
                                    ->count();
            $data[$i] = $batasBawah. " - ". $batasAtas;
            $batasBawah = $batasAtas + 1;

            //menghitung (f0-fe)^2/fe
            $kai[$i] = number_format(pow(($frekuensi[$i] - $fe[$i]),2)/$fe[$i], 7);
            $totalchi += $kai[$i];
        }



        return view ('statistik/chi-kuadrat', ['data' => $data,
                                        'frekuensi' => $frekuensi,
                                        'batasAtas' => $batasAtas,
                                        'batasBawah' => $batasBawah,
                                        'kelas' => $kelas,
                                        'interval' => $interval,
                                        'rentangan' => $rentangan,
                                        'batasBawahBaru' => $batasBawahBaru,
                                        'batasAtasBaru' => $batasAtasBaru,
                                        'zBawah' => $zBawah,
                                        'zAtas' => $zAtas,
                                        'zTabelBawahFix' => $zTabelBawahFix,
                                        'zTabelAtasFix' => $zTabelAtasFix,
                                        'lprop' => $lprop,
                                        'fe' => $fe,
                                        'kai' => $kai,
                                        'totalchi' => $totalchi,
                                        ]);
   }

   public function lilliefors(){

        //ngambil banyak skor
        $n = DataMahasiswa::count('nilai_mahasiswa');
        $rata2 = number_format(DataMahasiswa::average('nilai_mahasiswa'), 2);

        //function standar deviasi
        function std_deviation($my_arr){
            $no_element = count($my_arr);
            $var = 0.0;
            $avg = array_sum($my_arr)/$no_element;
            foreach($my_arr as $i)
                {
                    $var += pow(($i - $avg), 2);
                }
            return (float)sqrt($var/$no_element);
        }

        //function desimal
        function desimal($nilai){
            if($nilai<0){
                $des = substr($nilai,0,4);
            } else {
                $des = substr($nilai,0,3);
            }
            return $des;
        }

        //function label
        function label($nilai){
            if($nilai<0){
                $str1 = substr($nilai,4,1);
            } else {
                $str1 = substr($nilai,3,1);
            }

            switch($str1){
                case '0':
                    $sLabel = 'nol';
                    break;
                case '1':
                    $sLabel = 'satu';
                    break;
                case '2':
                    $sLabel = 'dua';
                    break;
                case '3':
                    $sLabel = 'tiga';
                    break;
                case '4':
                    $sLabel = 'empat';
                    break;
                case '5':
                    $sLabel = 'lima';
                    break;
                case '6':
                    $sLabel = 'enam';
                    break;
                case '7':
                    $sLabel = 'tujuh';
                    break;
                case '8':
                    $sLabel = 'delapan';
                    break;
                case '9':
                    $sLabel = 'sembilan';
                    break;
                default: $sLabel = 'Tidak ada field';
            }

            return $sLabel;
        }

        //ambil nilai skor
        $anggota = DataMahasiswa::select('nilai_mahasiswa')->get();

        //masukin skor ke dalam array biar bsa dipakek sama functionnya
        $i = 0;
        foreach ($anggota as $a){
            $arraySkor[$i] = $a->nilai_mahasiswa;
            $i++;
        }

        //standar deviasi dari seluruh skor
        $SD = number_format(std_deviation($arraySkor), 2);

        //ngambil data dan frekuensinya
        for($i = 0; $i < $n; $i++){
            $frekuensi[$i] = DataMahasiswa::select('nilai_mahasiswa', DB::raw('count(*) as frekuensi'))  //ambil skor, hitung banyak skor taruh di tabel frekuensi
                                ->groupBy('nilai_mahasiswa')    //urutkan sesuai skor
                                ->get();
            //ngambil banyak data setelah diambil frekuensinya
            $banyakData = count($frekuensi[$i]);
        }

        //mencari f(zi) dari tabel z
        $fkum = 0;
        $totalLillie = 0;
        for ($i = 0; $i < $banyakData; $i++){

            //frekuensi komulatif
            $fkum += $frekuensi[0][$i]->frekuensi;
            $fkum2[$i] = $fkum;

            //mencari nilai Zi
            $Zi[$i] = number_format(($frekuensi[0][$i]->nilai_mahasiswa - $rata2)/$SD, 2);

            //mencari F(zi)dari tabel z
            $cariDesimalZi = desimal($Zi[$i]);
            $labelZi = label($Zi[$i]);
            $zTabel = ZTabel::where('z', '=', $cariDesimalZi)->get();
            $fZi[$i] = $zTabel[0]->$labelZi;

            //mencari S(Zi)
            $sZi[$i] = $fkum2[$i]/$n;

            //mencari |F(Zi)-S(Zi)|
            $lilliefors[$i] = abs($fZi[$i]-$sZi[$i]);

            //total
            $totalLillie += $lilliefors[$i];
        }


        return view('statistik/lilliefors', ['frekuensi' => $frekuensi,
                                    'banyakData' => $banyakData,
                                    'fkum2' => $fkum2,
                                    'Zi' => $Zi,
                                    'fZi' => $fZi,
                                    'sZi' => $sZi,
                                    'lilliefors' => $lilliefors,
                                    'totalLillie' => $totalLillie,
                                    'n' => $n,
                                 ]);
   }
}
