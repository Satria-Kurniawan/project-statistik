@extends('layout/main')

@section('title', 'Data Bergolong')

@section('container')

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table text-center table-bordered">
                    <div class="col-12 container bg-primary text-light text-center p-1">
                        <h4>TABEL DATA BERGOLONG</h4>
                    </div>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Rentangan</th>
                            <th>Frekuensi</th>

                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < $kelas; $i++)

                        <tr>
                            <th> {{ $i+1 }} </th>
                            <td> {{ $data[$i] }}</td>
                            <td> {{ $frekuensi[$i] }}</td>
                        </tr>

                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
