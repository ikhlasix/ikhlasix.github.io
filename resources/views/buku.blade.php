@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            Master Buku
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <a href="#" id="add" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bukuModal">Tambah Buku</a>
                <div class="input-group" style="width: auto; display: inline-flex;">
                    <select id="field" class="form-select">
                        <option value="">Pilih Kolom...</option>
                        <option value="kode">ID</option>
                        <option value="judul">Judul</option>
                        <option value="pengarang">Pengarang</option>
                        <option value="tahun_terbit">Tahun Terbit</option>
                    </select>
                    <input type="text" id="value" class="form-control" placeholder="Inputkan Data...">
                    <button class="btn btn-primary" type="button" id="filterButton">Cari</button>
                    <button class="btn btn-secondary" type="button" id="resetButton">Reset</button>
                </div>
            </div>
            <table id="buku" class="table table-striped"></table>
            <div class="d-flex justify-content-end">
                <nav aria-label="...">
                    <ul class="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bukuModal" tabindex="-1" aria-labelledby="bukuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bukuModalLabel">Tambah Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bukuForm">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="kode" class="form-label">ID</label>
                            <input type="text" class="form-control" id="kode" name="kode">
                        </div>
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="judul" name="judul">
                        </div>
                        <div class="mb-3">
                            <label for="pengarang" class="form-label">Pengarang</label>
                            <input type="text" class="form-control" id="pengarang" name="pengarang">
                        </div>
                        <div class="mb-3">
                            <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                            <input type="text" class="form-control" id="tahun_terbit" name="tahun_terbit">
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="button" class="btn btn-primary" id="confirmDelete">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage">Terjadi kesalahan. Silakan coba lagi.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function loadTable (params='') {
            $.ajax({
                url: `{{ config('app.srv_url') }}/api/buku${params}`,
                type: 'GET',
                success: function (response) {
                    var metaData = response.metaData;
                    var paginationContent = '';
                    var totalPages = Math.ceil(metaData.total / metaData.limit);

                    paginationContent += `<li class="page-item ${metaData.page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${metaData.page - 1}">&laquo;</a>
                    </li>`;

                    if (totalPages <= 5) {
                        for (var i = 1; i <= totalPages; i++) {
                            paginationContent += `<li class="page-item ${metaData.page === i ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>`;
                        }
                    } else {
                        if (metaData.page <= 3) {
                            for (var i = 1; i <= 4; i++) {
                                paginationContent += `<li class="page-item ${metaData.page === i ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                                </li>`;
                            }
                            paginationContent += `<li class="page-item disabled">
                                <a class="page-link" href="#">...</a>
                            </li>`;
                            paginationContent += `<li class="page-item ${metaData.page === totalPages ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                            </li>`;
                        } else if (metaData.page > totalPages - 2) {
                            paginationContent += `<li class="page-item ${metaData.page === 1 ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="1">1</a>
                            </li>`;
                            paginationContent += `<li class="page-item disabled">
                                <a class="page-link" href="#">...</a>
                            </li>`;
                            for (var i = totalPages - 2; i <= totalPages; i++) {
                                paginationContent += `<li class="page-item ${metaData.page === i ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                                </li>`;
                            }
                        } else {
                            paginationContent += `<li class="page-item ${metaData.page === 1 ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="1">1</a>
                            </li>`;
                            paginationContent += `<li class="page-item disabled">
                                <a class="page-link" href="#">...</a>
                            </li>`;
                            for (var i = metaData.page - 1; i <= metaData.page + 1; i++) {
                                paginationContent += `<li class="page-item ${metaData.page === i ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                                </li>`;
                            }
                            paginationContent += `<li class="page-item disabled">
                                <a class="page-link" href="#">...</a>
                            </li>`;
                            paginationContent += `<li class="page-item ${metaData.page === totalPages ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                            </li>`;
                        }
                    }

                    paginationContent += `<li class="page-item ${metaData.page === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${metaData.page + 1}">&raquo;</a>
                    </li>`;

                    $('.pagination').html(paginationContent);

                    $('.pagination a').click(function(e) {
                        e.preventDefault();
                        var page = $(this).data('page');
                        loadTable(`?page=${page}&limit=${metaData.limit}`);
                    });

                    var tableContent = '';
                    if (response.data.length === 0) {
                        tableContent = `<tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>`;
                    } else {
                        response.data.forEach(function(buku) {
                            tableContent += `<tr>
                                <td>${buku.kode}</td>
                                <td>${buku.judul}</td>
                                <td>${buku.pengarang}</td>
                                <td>${buku.tahun_terbit}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="#" id="edit" class="btn btn-primary">Edit</a>
                                        <a href="#" id="delete" class="btn btn-secondary">Hapus</a>
                                    </div>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#buku').html(`
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Tahun Terbit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableContent}
                        </tbody>
                    `);
                },
                error: function (error) {
                    console.error(error);
                    $('#errorModal').modal('show');
                },
            });
        }
        $(document).ready(function(e) {

            var page = 1;
            var limit = 10;
            var params = `?page=${page}&limit=${limit}`;
            loadTable(params);
            
            $('#filterButton').click(function(e) {
                var field = $('#field').val();
                var value = $('#value').val();
                var params = `?field=${field}&is_like=${field !== 'tahun_terbit'}&value=${value}&page=${page}&limit=${limit}`;
                loadTable(params);
            });

            $(document).on('click', '#add', function(e) {
                $('#id').val('');
                $('#kode').prop('readonly', false).css('background-color', '#ffffff');
                $('#kode').val('');
                $('#judul').val('');
                $('#pengarang').val('');
                $('#tahun_terbit').val('');
                $('#bukuModal').modal('show');
                $('#bukuModalLabel').text('Tambah Buku');
            });

            $('#bukuForm').submit(function(e) {
                e.preventDefault();
                var url = `{{ config('app.srv_url') }}/api/buku`;
                var type = 'POST';

                if ($('#id').val() !== '') {
                    url = `{{ config('app.srv_url') }}/api/buku/${$('#id').val()}`;
                    type = 'PUT';
                }

                var data = JSON.stringify({
                    kode: $('#kode').val(),
                    judul: $('#judul').val(),
                    pengarang: $('#pengarang').val(),
                    tahun_terbit: $('#tahun_terbit').val(),
                });
                $.ajax({
                    url: url,
                    type: type,
                    contentType: 'application/json',
                    data: data,
                    success: function (response) {
                        $('#bukuModal').modal('hide');
                        loadTable(params);
                    },
                    error: function (error) {
                        console.error(error);
                        $('#bukuModal').modal('hide');
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#edit', function(e) {
                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/buku/detail?field=kode&is_like=false&value=${$(this).closest('tr').find('td').eq(0).text()}`,
                    type: 'GET',
                    success: function (response) {
                        $('#id').val(response.data.id);
                        $('#kode').prop('readonly', true).css('background-color', '#e9ecef');
                        $('#kode').val(response.data.kode);
                        $('#judul').val(response.data.judul);
                        $('#pengarang').val(response.data.pengarang);
                        $('#tahun_terbit').val(response.data.tahun_terbit);

                        $('#bukuModal').modal('show');
                        $('#bukuModalLabel').text('Ubah Buku');
                    },
                    error: function (error) {
                        console.error(error);
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#delete', function(e) {
                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/buku/detail?field=kode&is_like=false&value=${$(this).closest('tr').find('td').eq(0).text()}`,
                    type: 'GET',
                    success: function (response) {
                        $('#deleteId').val(response.data.id);
                        $('#deleteModal').modal('show');
                    },
                    error: function (error) {
                        console.error(error);
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#confirmDelete', function(e) {
                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/buku/${$('#deleteId').val()}`,
                    type: 'DELETE',
                    success: function (response) {
                        $('#deleteModal').modal('hide');
                        loadTable();
                    },
                    error: function (error) {
                        console.error(error);
                        $('#deleteModal').modal('hide');
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#resetButton', function(e) {
                $('#field').val('');
                $('#value').val('');
                loadTable();
            });

        });
    </script>
@endsection