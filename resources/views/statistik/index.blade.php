
@extends('layout/main')

@section('title', 'Statistik')

@section('container')

    <div class="float-right">
        <a href="{{ route('export') }}" class="btn btn-outline-success">Export</a>
        <a href="#" class="btn btn-outline-warning mr-4" data-toggle="modal" data-target="#exampleModalLong">Import</a>
    </div>

    <div class="">
        <label for="min" class="ml-2">Skor min : <b>{{ $min }}</b></label>
        <label for="max" class="ml-4">Skor max : <b>{{ $max }}</b></label>
        <label for="rata2" class="ml-4">Rata-rata : <b>{{ $rata2 }}</b></label>
    </div>

    <div class="row col-12">
        <div class="container col-12">
            <form action="/statistik" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- <div class="form-group mt-3">
                    <label for="nama">Nama Mahasiswa:</label>
                    <input type="text" class="form-control" placeholder="Masukan nama" name="nama" id="nama">
                </div> --}}
                <div class="form-group">
                    <input type="hidden" class="form-control" name="id" id="id">
                </div>
                <div class="form-group">
                    <label for="nilai">Skor :</label>
                    <input type="number" class="form-control" placeholder="Masukan skor" name="nilai" id="nilai">
                </div>
                <button type="submit" class="btn btn-outline-primary mb-3 float-right px-3">Save</button>
                {{-- <label for="min" class="ml-4">Skor min : <b>{{ $min }}</b></label>
                <label for="max" class="ml-4">Skor max : <b>{{ $max }}</b></label>
                <label for="rata2" class="ml-4">Rata-rata : <b>{{ $rata2 }}</b></label> --}}
            </form>
        </div>
    </div>


        {{-- @foreach ($mahasiswa as $data)
          <h4>No : {{ $data['No'] }}</h4><br>
          <h4>Nama : {{ $data['Nama'] }}</h4><br>
          <h4>Nilai : {{ $data['Nilai'] }}</h4><br>
        @endforeach --}}

@if (session('pesan'))
    <div class="row col-12">
        <div class="container col-12">
            <div class="alert alert-success">{{ session('pesan') }}</div>
        </div>
    </div>
@endif

  <!-- Modal -->
  <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Import File</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
    <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="form-group">
                {{-- {{ csrf_field() }} --}}
                <div class="form-group">
                    <input type="file" name="file" required="required">
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>

        </div>
    </form>
      </div>
    </div>
  </div>

<div class="row justify-content-center">
    <div class="col-7">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <div class="col-12 container bg-primary text-light text-center p-1">
                        <h4>TABEL NILAI</h4>
                    </div>
                <thead>
                    <tr>
                        <th class="col-2">id</th>
                        {{-- <th class="col-6">Nama</th> --}}
                        <th class="col-5">Skor</th>
                        <th class="col-5">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mahasiswa as $data)
                    <tr>
                        <td>{{ $data->id_mahasiswa }}</td>
                        {{-- <td>{{ $data->nama_mahasiswa }}</td> --}}
                        <td>{{ $data->nilai_mahasiswa }}</td>
                        <td>
                            <a href="/statistik/edit/{{ $data->id_mahasiswa }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            {{-- <a href="/statistik/delete/{{ $data->id_mahasiswa }}" class="btn btn-sm btn-outline-danger" id="delete">Delete</a> --}}
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteModal{{ $data->id_mahasiswa }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-5">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <div class="col-12 container bg-primary text-light text-center p-1">
                        <h4>TABEL FREKUENSI</h4>
                    </div>
                    <thead>
                        <tr>
                            <th class="col-6">Nilai</th>
                            <th class="col-6">Frekuensi</th>
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
</div>

@foreach ($mahasiswa as $data)
    <!-- Modal -->
    <div class="modal fade" id="deleteModal{{ $data->id_mahasiswa }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Id {{$data->id_mahasiswa}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Yakin mau ngapus?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <a href="/statistik/delete/{{ $data->id_mahasiswa }}">
                        <button type="button" class="btn btn-success">Yes</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

