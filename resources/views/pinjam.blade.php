@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            Peminjaman Buku
        </div>
        <div class="card-body">
            <form id="pinjamForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <select id="nim" class="form-select">
                            <option value="">Pilih NIM</option>
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" class="form-control" disabled>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <input type="text" id="jenis_kelamin" class="form-control" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="no_telepon" class="form-label">No Telepon</label>
                        <input type="text" id="no_telepon" class="form-control" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h5>List Buku</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Judul Buku</th>
                                    <th>Tanggal Awal</th>
                                    <th>Tanggal Akhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <select id="buku_select" class="form-select">
                                            <option value="">Pilih Judul Buku</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" id="addbuku" class="btn btn-primary">Tambah</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="successMessage">Data berhasil disimpan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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

        $(document).ready(function(e) {

            $.ajax({
                url: `{{ config('app.srv_url') }}/api/mahasiswa`,
                method: 'GET',
                success: function(response) {
                    var nimSelect = $('#nim');
                    nimSelect.empty();
                    nimSelect.append('<option value="">Pilih NIM</option>');
                    $.each(response.data, function(index, mahasiswa) {
                        if (mahasiswa.status === true) {
                            nimSelect.append(`<option value="${mahasiswa.id}">${mahasiswa.nim} - ${mahasiswa.nama}</option>`);
                        }
                    });
                },
                error: function() {
                    $('#errorMessage').text('Gagal mengambil data NIM. Silakan coba lagi.');
                    $('#errorModal').modal('show');
                }
            });

            $('#nim').on('change', function() {
                var nim = $(this).val();
                if (nim === '') {
                    $('#nama').val('');
                    $('#jenis_kelamin').val('');
                    $('#no_telepon').val('');
                    $('#email').val('');
                    return;
                }

                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/mahasiswa/detail?field=id&is_like=false&value=${nim}`,
                    method: 'GET',
                    success: function(response) {
                        console.lo
                        $('#nama').val(response.data.nama);
                        $('#jenis_kelamin').val(response.data.jenis_kelamin === "L" ? "Laki-laki" : "Perempuan");
                        $('#no_telepon').val(response.data.no_telepon);
                        $('#email').val(response.data.email);
                    },
                    error: function() {
                        $('#errorMessage').text('Gagal mengambil data mahasiswa. Silakan coba lagi.');
                        $('#errorModal').modal('show');
                    }
                });
            });

            $.ajax({
                url: `{{ config('app.srv_url') }}/api/inventory`,
                method: 'GET',
                success: function(response) {
                    var bukuSelect = $('#buku_select');
                    bukuSelect.empty();
                    bukuSelect.append('<option value="">Pilih Judul Buku</option>');
                    $.each(response.data, function(index, inv) {
                        if (inv.jumlah > 0) {
                            bukuSelect.append(`<option value="${inv.buku.id}"> ${inv.buku.judul} (${inv.buku.pengarang}, ${inv.buku.tahun_terbit}) </option>`);
                        }
                    });
                },
                error: function() {
                    $('#errorMessage').text('Gagal mengambil data buku. Silakan coba lagi.');
                    $('#errorModal').modal('show');
                }
            });

            $('#addbuku').on('click', function() {
                var bukuSelect = $('#buku_select');
                var selectedBuku = bukuSelect.find('option:selected');
                var bukuId = selectedBuku.val();
                var bukuText = selectedBuku.text();
                var tanggalAwal = new Date().toISOString().split('T')[0];
                var tanggalAkhir = new Date();
                tanggalAkhir.setDate(tanggalAkhir.getDate() + 14);
                tanggalAkhir = tanggalAkhir.toISOString().split('T')[0];

                if (bukuId === '') {
                    $('#errorMessage').text('Silakan pilih judul buku.');
                    $('#errorModal').modal('show');
                    return;
                }

                var isDuplicate = false;
                $('input[name="buku_id[]"]').each(function() {
                    if ($(this).val() == bukuId) {
                        isDuplicate = true;
                        return false;
                    }
                });

                if (isDuplicate) {
                    $('#errorMessage').text('Buku sudah dipilih. Silakan pilih buku lain.');
                    $('#errorModal').modal('show');
                    return;
                }

                var newRow = `
                    <tr>
                        <td>
                            <input type="hidden" name="buku_id[]" value="${bukuId}">
                            ${bukuText}
                        </td>
                        <td>
                            <input type="hidden" name="tanggal_pinjam[]" value="${tanggalAwal}">
                            ${tanggalAwal}
                        </td>
                        <td>
                            <input type="hidden" name="tanggal_kembali[]" value="${tanggalAkhir}">
                            ${tanggalAkhir}
                        </td>
                        <td>
                            <button type="button" class="btn btn-secondary removebuku">Hapus</button>
                        </td>
                    </tr>
                `;

                $('table tbody').append(newRow);
                bukuSelect.val('');
            });

            $('#pinjamForm').on('submit', function(e) {
                e.preventDefault();

                var nim = $('#nim').val();
                if (nim === "") {
                    $('#errorMessage').text('Data mahasiwa kosong');
                    $('#errorModal').modal('show');
                    return;
                }

                var pinjamData = [];
                $('table tbody tr').each(function() {
                    var bukuId = $(this).find('input[name="buku_id[]"]').val();
                    var tanggal_pinjam = $(this).find('input[name="tanggal_pinjam[]"]').val();
                    var tanggal_kembali = $(this).find('input[name="tanggal_kembali[]"]').val();
                    if (bukuId && tanggal_pinjam && tanggal_kembali) {
                        pinjamData.push({
                            mahasiswa_id: nim,
                            buku_id: bukuId,
                            tanggal_pinjam: tanggal_pinjam,
                            tanggal_kembali: tanggal_kembali
                        });
                    }
                });
                
                if (pinjamData.length === 0) {
                    $('#errorMessage').text('Data buku kosong');
                    $('#errorModal').modal('show');
                    return;
                }

                var data = JSON.stringify({ pinjamData });

                $.ajax({
                    url: `{{ config('app.srv_url') }}/api/pinjam`,
                    method: 'POST',
                    contentType: 'application/json',
                    data: data,
                    success: function(response) {
                        $('#successModal').modal('show');
                        $('#successModal').on('hidden.bs.modal', function () {
                            window.location.href = "/laporan";
                        });
                    },
                    error: function(error) {
                        $('#errorMessage').text(error.responseJSON.responseMessage);
                        $('#errorModal').modal('show');
                    }
                });
            });

            $(document).on('click', '.removebuku', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>
@endsection