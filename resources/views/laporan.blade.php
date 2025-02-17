@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            History Peminjaman Buku
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" id="nim" placeholder="NIM">
                    <input type="text" class="form-control" id="nama" placeholder="Nama Mahasiswa">
                    <input type="text" class="form-control" id="kode" placeholder="Kode Buku">
                    <input type="text" class="form-control" id="judul" placeholder="Judul Buku">
                    <input type="date" class="form-control" id="tanggal_pinjam" placeholder="Tanggal Pinjam">
                    <input type="date" class="form-control" id="tanggal_kembali" placeholder="Tanggal Kembali">
                    <button class="btn btn-primary" id="filterButton">Filter</button>
                    <button class="btn btn-secondary" id="resetButton">Reset</button>
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
                url: `{{ config('app.srv_url') }}/api/laporan${params}`,
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
                        response.data.forEach(function(laporan) {
                            tableContent += `<tr>
                                <td>${laporan.mahasiswa.nim}</td>
                                <td>${laporan.mahasiswa.nama}</td>
                                <td>${laporan.buku.kode}</td>
                                <td>${laporan.buku.judul}</td>
                                <td>${laporan.tanggal_pinjam}</td>
                                <td>${laporan.tanggal_kembali}</td>
                                <td>${Math.ceil((new Date(laporan.tanggal_kembali) - new Date(laporan.tanggal_pinjam)) / (1000 * 60 * 60 * 24))} hari</td>
                            </tr>`;
                        });
                    }
                    $('#buku').html(`
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Kode Buku</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Lama Pinjam</th>
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
                e.preventDefault();
                var nim = $('#nim').val();
                var nama = $('#nama').val();
                var kode = $('#kode').val();
                var judul = $('#judul').val();
                var tanggal_pinjam = $('#tanggal_pinjam').val();
                var tanggal_kembali = $('#tanggal_kembali').val();

                var params = `?page=1&limit=10`;

                if (nim) params += `&mahasiswa:nim=${nim}`;
                if (nama) params += `&mahasiswa:nama=${nama}`;
                if (kode) params += `&buku:kode=${kode}`;
                if (judul) params += `&buku:judul=${judul}`;
                if (tanggal_pinjam) params += `&tanggal_pinjam=${tanggal_pinjam}`;
                if (tanggal_kembali) params += `&tanggal_kembali=${tanggal_kembali}`;

                loadTable(params);
            });

            $(document).on('click', '#resetButton', function(e) {
                $('#nim').val('');
                $('#nama').val('');
                $('#judul').val('');
                $('#kode').val('');
                $('#tanggal_pinjam').val('');
                $('#tanggal_kembali').val('');
                loadTable();
            });

        });
    </script>
@endsection