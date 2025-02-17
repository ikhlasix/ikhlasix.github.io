<?php

use Illuminate\Support\Facades\Route;

Route::get('/buku', function () {
    return view('buku');
});

Route::get('/mahasiswa', function () {
    return view('mahasiswa');
});

Route::get('/inventory', function () {
    return view('inventory');
});

Route::get('/', function () {
    return view('pinjam');
});

Route::get('/laporan', function () {
    return view('laporan');
});
