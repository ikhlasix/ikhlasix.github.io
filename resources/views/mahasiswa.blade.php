@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            Master Mahasiswa
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <a href="#" id="add" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mahasiswaModal">Tambah Mahasiswa</a>
                <div class="input-group" style="width: auto; display: inline-flex;">
                    <select id="field" class="form-select">
                        <option value="">Pilih Kolom...</option>
                        <option value="nim">NIM</option>
                        <option value="nama">Nama</option>
                        <option value="jenis_kelamin">Jenis Kelamin</option>
                        <option value="email">Email</option>
                        <option value="no_telepon">No Telepon</option>
                        <option value="status">Status</option>
                    </select>
                    <input type="text" id="value" class="form-control" placeholder="Inputkan Data...">
                    <button class="btn btn-primary" type="button" id="filterButton">Cari</button>
                    <button class="btn btn-secondary" type="button" id="resetButton">Reset</button>
                </div>
            </div>
            <table id="mahasiswa" class="table table-striped"></table>
            <div class="d-flex justify-content-end">
                <nav aria-label="...">
                    <ul class="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mahasiswaModal" tabindex="-1" aria-labelledby="mahasiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mahasiswaModalLabel">Tambah Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mahasiswaForm">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" class="form-control" id="nim" name="nim" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" value="true">
                                <label class="form-check-label" for="status">Aktif</label>
                            </div>
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
                url: `{{ config('app.srv_url') }}/api/mahasiswa${params}`,
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
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>`;
                    } else {
                        response.data.forEach(function(mahasiswa) {
                            tableContent += `<tr>
                                <td>${mahasiswa.nim}</td>
                                <td>${mahasiswa.nama}</td>
                                <td>${mahasiswa.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                                <td>${mahasiswa.email}</td>
                                <td>${mahasiswa.no_telepon}</td>
                                <td>
                                    <span class="badge ${mahasiswa.status ? 'bg-primary' : 'bg-secondary'}">
                                        ${mahasiswa.status ? 'Aktif' : 'Non Aktif'}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="#" id="edit" class="btn btn-primary">Edit</a>
                                        <a href="#" id="delete" class="btn btn-secondary">Hapus</a>
                                    </div>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#mahasiswa').html(`
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Email</th>
                                <th>No Telepon</th>
                                <th>Status</th>
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

            loadTable();
            $('#filterButton').click(function(e) {
                var field = $('#field').val();
                var value = $('#value').val();
                var params = `?field=${field}&is_like=true&value=${value}`;
                loadTable(params);
            });

            $(document).on('click', '#add', function(e) {
                $('#id').val('');
                $('#nim').prop('readonly', false).css('background-color', '#ffffff');
                $('#nim').val('');
                $('#nama').val('');
                $('#jenis_kelamin').val('L');
                $('#email').val('');
                $('#no_telepon').val('');
                $('#mahasiswaModal').modal('show');
                $('#mahasiswaModalLabel').text('Tambah Mahasiswa');
            });

            $('#mahasiswaForm').submit(function(e) {
                e.preventDefault();
                var url = `{{ config('app.srv_url') }}/api/mahasiswa`;
                var type = 'POST';

                if ($('#id').val() !== '') {
                    url = `{{ config('app.srv_url') }}/api/mahasiswa/${$('#id').val()}`;
                    type = 'PUT';
                }

                var data = JSON.stringify({
                    nim: $('#nim').val(),
                    nama: $('#nama').val(),
                    jenis_kelamin: $('#jenis_kelamin').val(),
                    email: $('#email').val(),
                    no_telepon: $('#no_telepon').val(),
                    status: $('#status').is(':checked') ? true : false
                });
                $.ajax({
                    url: url,
                    type: type,
                    contentType: 'application/json',
                    data: data,
                    success: function (response) {
                        $('#mahasiswaModal').modal('hide');
                        loadTable();
                    },
                    error: function (error) {
                        console.error(error);
                        $('#mahasiswaModal').modal('hide');
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#edit', function(e) {
                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/mahasiswa/detail?field=nim&is_like=false&value=${$(this).closest('tr').find('td').eq(0).text()}`,
                    type: 'GET',
                    success: function (response) {
                        $('#id').val(response.data.id);
                        $('#nim').prop('readonly', true).css('background-color', '#e9ecef');
                        $('#nim').val(response.data.nim);
                        $('#nama').val(response.data.nama);
                        $('#jenis_kelamin').val(response.data.jenis_kelamin);
                        $('#email').val(response.data.email);
                        $('#no_telepon').val(response.data.no_telepon);
                        $('#status').prop('checked', response.data.status);

                        $('#mahasiswaModal').modal('show');
                        $('#mahasiswaModalLabel').text('Ubah Mahasiswa');
                    },
                    error: function (error) {
                        console.error(error);
                        $('#mahasiswaModal').modal('hide');
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#delete', function(e) {
                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/mahasiswa/detail?field=nim&is_like=false&value=${$(this).closest('tr').find('td').eq(0).text()}`,
                    type: 'GET',
                    success: function (response) {
                        $('#deleteId').val(response.data.id);
                        $('#deleteModal').modal('show');
                    },
                    error: function (error) {
                        console.error(error);
                        $('#deleteModal').modal('hide');
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#confirmDelete', function(e) {
                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/mahasiswa/${$('#deleteId').val()}`,
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