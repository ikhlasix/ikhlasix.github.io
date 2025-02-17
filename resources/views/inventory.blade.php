@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            Inventory
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <a href="#" id="add" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inventoryModal">Tambah Inventory</a>
                <div class="input-group" style="width: auto; display: inline-flex;">
                    <select id="field" class="form-select">
                        <option value="">Pilih Kolom...</option>
                        <option value="nama_rak">Nama Rak</option>
                        <option value="buku:judul">Judul Buku</option>
                        <option value="buku:pengarang">Pengarang Buku</option>
                        <option value="buku:tahun_terbit">Tahun Terbit Buku</option>
                    </select>
                    <input type="text" id="value" class="form-control" placeholder="Inputkan Data...">
                    <button class="btn btn-primary" type="button" id="filterButton">Cari</button>
                    <button class="btn btn-secondary" type="button" id="resetButton">Reset</button>
                </div>
            </div>
            <table id="inventory" class="table table-striped"></table>
            <div class="d-flex justify-content-end">
                <nav aria-label="...">
                    <ul class="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inventoryModalLabel">Tambah Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="inventoryForm">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label for="buku_id" class="form-label">Judul Buku</label>
                            <select class="form-select" aria-label="Judul Buku" name="buku_id" id="buku_id">
                                <option value="">Pilih Buku...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="rak_id" class="form-label">Nama Rak</label>
                            <select class="form-select" aria-label="Nama Rak" name="nama_rak" id="nama_rak">
                                <option value="">Pilih Rak...</option>
                            </select>
                        </div>
                        <div class="mb-3 row">
                            <label for="jumlah" class="col-sm-8 col-form-label">Jumlah</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="decrement">-</button>
                                    <input type="text" class="form-control text-center" id="jumlah" name="jumlah" value="1" required>
                                    <button class="btn btn-outline-secondary" type="button" id="increment">+</button>
                                </div>
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
        const NAMA_RAK = [
            "AA-001",
            "AA-002",
            "AA-003",
            "AA-004",
            "AA-005",
            "AB-001",
            "AB-002",
            "AB-003",
            "AB-004",
            "AB-005",
            "AC-001",
            "AC-002",
            "AC-003",
            "AC-004",
            "AC-005",
        ];
                
        function loadTable (params='') {
            $.ajax({
                url: `{{ config('app.srv_url') }}/api/inventory${params}`,
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
                            <td colspan="4" class="text-center">Tidak ada data</td>
                        </tr>`;
                    } else {
                        response.data.forEach(function(inventory) {
                            tableContent += `<tr>
                                <td>${inventory.buku.judul} (${inventory.buku.pengarang}, ${inventory.buku.tahun_terbit})</td>
                                <td>${inventory.jumlah}</td>
                                <td>${inventory.nama_rak}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="#" id="edit" class="btn btn-primary" data-id="${inventory.id}">Update Stok</a>
                                    </div>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#inventory').html(`
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Jumlah</th>
                                <th>Nama Rak</th>
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

            $('#field').change(function(e) {
                if ($('#field').val() === 'nama_rak') {
                    let options = '<option value="">Pilih Rak...</option>';
                    NAMA_RAK.forEach(function(rak) {
                        options += `<option value="${rak}">${rak}</option>`;
                    });
                    $('#value').replaceWith(`<select class="form-select" id="value">${options}</select>`);
                } else {
                    $('#value').replaceWith('<input type="text" id="value" class="form-control" placeholder="Inputkan Data...">');
                }
            });

            $(document).on('click', '#add', function(e) {
                let options_rak = '<option value="">Pilih Rak...</option>';
                NAMA_RAK.forEach(function(rak) {
                    options_rak += `<option value="${rak}">${rak}</option>`;
                });
                $('#nama_rak').replaceWith(`<select class="form-select" aria-label="Nama Rak" name="nama_rak" id="nama_rak">${options_rak}</select>`);

                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/buku`,
                    type: 'GET',
                    success: function (response) {
                        let options_buku = '<option value="">Pilih Buku...</option>';
                        response.data.forEach(function(buku) {
                            options_buku += `<option value="${buku.id}">${buku.judul} (${buku.pengarang}, ${buku.tahun_terbit})</option>`;
                        });
                        $('#buku_id').replaceWith(`<select class="form-select" aria-label="Judul Buku" name="buku_id" id="buku_id">${options_buku}</select>`);
                    },
                    error: function (error) {
                        console.error(error);
                        $('#errorModal').modal('show');
                    },
                });

                $('#inventoryModalLabel').text('Tambah Inventory');
                $('#jumlah').val('1');
            });

            $('#jumlah').on('input', function(e) {
                this.value = this.value.replace(/\D/g, '');
            });

            $('#increment').click(function(e) {
                var jumlah = parseInt($('#jumlah').val());
                $('#jumlah').val(jumlah + 1);
            });

            $('#decrement').click(function(e) {
                var jumlah = parseInt($('#jumlah').val());
                if (jumlah > 1) {
                    $('#jumlah').val(jumlah - 1);
                }
            });

            $('#inventoryForm').submit(function(e) {
                e.preventDefault();
                var url = `{{ config('app.srv_url') }}/api/inventory`;
                var type = 'POST';

                if ($('#id').val() !== '') {
                    url = `{{ config('app.srv_url') }}/api/inventory/${$('#id').val()}`;
                    type = 'PUT';
                }

                var data = JSON.stringify({
                    buku_id: $('#buku_id').val(),
                    nama_rak: $('#nama_rak').val(),
                    jumlah: $('#jumlah').val(),
                });
                $.ajax({
                    url: url,
                    type: type,
                    contentType: 'application/json',
                    data: data,
                    success: function (response) {
                        $('#inventoryModal').modal('hide');
                        loadTable();
                    },
                    error: function (error) {
                        console.error(error);
                        $('#inventoryModal').modal('hide');
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#edit', function(e) {
                var id = $(this).data('id');
                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/inventory/detail?field=id&is_like=false&value=${id}`,
                    type: 'GET',
                    success: function (response) {
                        $('#id').val(response.data.id);
                        $.ajax({
                            url: `{{ config('app.srv_url') }}/api/buku/detail?field=id&is_like=false&value=${response.data.buku_id}`,
                            type: 'GET',
                            success: function (bukuResponse) {
                                let options_buku = `<option value="${bukuResponse.data.id}">${bukuResponse.data.judul} (${bukuResponse.data.pengarang}, ${bukuResponse.data.tahun_terbit})</option>`;
                                $('#buku_id').replaceWith(`<select class="form-select" aria-label="Judul Buku" name="buku_id" id="buku_id">${options_buku}</select>`);
                                $('#buku_id').val(bukuResponse.data.id);
                            },
                            error: function (error) {
                                console.error(error);
                                $('#errorModal').modal('show');
                            },
                        });
                        
                        let options_rak = `<option value="${response.data.nama_rak}">${response.data.nama_rak}</option>`;
                        $('#nama_rak').replaceWith(`<select class="form-select" aria-label="Nama Rak" name="nama_rak" id="nama_rak">${options_rak}</select>`);
                        $('#nama_rak').val(response.data.nama_rak);
                        $('#jumlah').val(response.data.jumlah);

                        $('#inventoryModal').modal('show');
                        $('#inventoryModalLabel').text('Ubah Inventory');
                    },
                    error: function (error) {
                        console.error(error);
                        $('#inventoryModal').modal('hide');
                        $('#errorModal').modal('show');
                    },
                });
            });

            $(document).on('click', '#delete', function(e) {
                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/inventory/detail?field=nim&is_like=false&value=${$(this).closest('tr').find('td').eq(0).text()}`,
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
                    url: `{{ config('app.srv_url') }}/api/inventory/${$('#deleteId').val()}`,
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