
@extends('layout/main')

@section('title', 'Statistik')

@section('container')
  <div class="container bg-light mt-1">
    <div class="row col-12">
        <form action="/statistik" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="form-group mt-3">
            <label for="nama">Nama Mahasiswa:</label>
            <input type="text" class="form-control" placeholder="Masukan nama" name="nama" id="nama">
          </div>
          <div class="form-group">
            <label for="nilai">Nilai:</label>
            <input type="number" class="form-control" placeholder="Masukan nilai" name="nilai" id="nilai">
          </div>
          <button type="submit" class="btn btn-primary mt-3 mb-3">Add</button>
          <label for="min" class="ml-4">Nilai min : <b>{{ $min }}</b></label>
          <label for="max" class="ml-4">Nilai max : <b>{{ $max }}</b></label>
          <label for="rata2" class="ml-4">Rata-rata : <b>{{ $rata2 }}</b></label>
        </form> 
    </div>
  </div>
  

        {{-- @foreach ($mahasiswa as $data)
          <h4>No : {{ $data['No'] }}</h4><br>
          <h4>Nama : {{ $data['Nama'] }}</h4><br>
          <h4>Nilai : {{ $data['Nilai'] }}</h4><br>
        @endforeach --}}     

@if (session('pesan'))
  <div class="container mt-3">
    <div class="row">
      <div class="alert alert-success">{{ session('pesan') }}</div>
    </div>
  </div>
@endif
        
<div class="container mt-1 bg-light">     
  <div class="row col-12">
    <table class="table table-bordered m-2">
      <div class="container bg-primary text-light text-center m-2">
        <h2>TABEL DATA MAHASISWA</h2>
      </div> 
      <thead>
        <tr>
          <th>Nama</th>
          <th class="col-2">Nilai</th>
          <th class="col-2">Opsi</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($mahasiswa as $data)
          <tr>
            <td>{{ $data->nama_mahasiswa }}</td>
            <td>{{ $data->nilai_mahasiswa }}</td>
            <td>
              <a href="/statistik/edit/{{ $data->id_mahasiswa }}" class="btn btn-sm btn-secondary">Edit</a>
              <a href="/statistik/delete/{{ $data->id_mahasiswa }}" class="btn btn-sm btn-danger" id="delete">Delete</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>  
  </div>
</div>    

<div class="container">
  <div class="row">
      <div class="col-md-4 mr-auto">
          <div class="row">
              <div class="col-12 bg-white form-container">
                <div class="container bg-primary text-light text-center">
                  <h2>TABEL FREKUENSI</h2>
                </div> 
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>Nilai</th>
                              <th>Frekuensi</th>
                          </tr>
                     </thead>
                     <tbody>
                         @foreach ($frekuensi as $nilai_mahasiswa)
                         
                         <tr>
                             <td> {{ $nilai_mahasiswa->nilai_mahasiswa }} </td>
                             <td> {{ $nilai_mahasiswa->frekuensi }}</td>
                          </tr>
                           
                          @endforeach
                          <tr>
                            <th>Total frekuensi :</th>
                            <td> {{ $totalfrekuensi }}</td>
                          </tr>
                          <tr>
                            <th>Total nilai :</td>
                            <td> {{ $totalskor }}</td>
                          </tr>
                     </tbody>
                  </table>
              </div>
          </div>
      </div>

@endsection
