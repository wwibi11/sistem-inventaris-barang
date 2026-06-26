<?php
require_once __DIR__ . '/../../config/database.php';


// TAMPILKAN PESAN DARI SESSION

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['import_message'])) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 8px; border: 1px solid #c3e6cb;'>
            <strong>✅ " . nl2br(htmlspecialchars($_SESSION['import_message'])) . "</strong>
          </div>";
    unset($_SESSION['import_message']);
}

if (isset($_SESSION['import_error'])) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 8px; border: 1px solid #f5c6cb;'>
            <strong>❌ " . nl2br(htmlspecialchars($_SESSION['import_error'])) . "</strong>
          </div>";
    unset($_SESSION['import_error']);
}

// DATA

$data = $pdo->query("
  SELECT
    keluarga.id,
    keluarga.no_kk,
    keluarga.nama_kepala_keluarga,
    keluarga.nik_ayah,
    keluarga.nama_ayah,
    keluarga.nik_ibu,
    keluarga.nama_ibu,
    keluarga.alamat,
    keluarga.rt,
    keluarga.rw,
    keluarga.desa,
    keluarga.kecamatan,
    keluarga.no_hp,
    COUNT(anak.id) AS jumlah_anak
  FROM keluarga
  LEFT JOIN anak
    ON anak.id_keluarga = keluarga.id
  GROUP BY
    keluarga.id,
    keluarga.no_kk,
    keluarga.nama_kepala_keluarga,
    keluarga.nik_ayah,
    keluarga.nama_ayah,
    keluarga.nik_ibu,
    keluarga.nama_ibu,
    keluarga.alamat,
    keluarga.rt,
    keluarga.rw,
    keluarga.desa,
    keluarga.kecamatan,
    keluarga.no_hp
  ORDER BY keluarga.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Hitung total
$total_keluarga = count($data);
$total_anak = array_sum(array_column($data, 'jumlah_anak'));
?>

<style>
/* ============================================
   STYLE DASHBOARD KELUARGA
   ============================================ */

/* Container */
.keluarga-container {
    padding: 10px 0;
}

/* Header */
.keluarga-header {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.keluarga-header .header-left h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    margin: 0;
}

.keluarga-header .header-left h4 i {
    color: #2c6b9e;
    margin-right: 10px;
}

.keluarga-header .header-left .sub-title {
    font-size: 13px;
    color: #8a94a6;
    margin-top: 2px;
}

.keluarga-header .header-right {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

/* Stat Cards */
.stat-mini {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8f9fc;
    padding: 8px 16px;
    border-radius: 10px;
    border: 1px solid #edf2f7;
}

.stat-mini .stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #ffffff;
}

.stat-mini .stat-icon.primary { background: #2c6b9e; }
.stat-mini .stat-icon.success { background: #28a745; }

.stat-mini .stat-number {
    font-size: 18px;
    font-weight: 700;
    color: #1a2634;
    line-height: 1.2;
}

.stat-mini .stat-label {
    font-size: 10px;
    color: #8a94a6;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Button Tambah */
.btn-tambah {
    background: #2c6b9e;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-tambah:hover {
    background: #1f507a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 107, 158, 0.25);
}

/* Card Utama */
.card-keluarga {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}

.card-keluarga .card-body {
    padding: 20px 22px;
}

/* Search Box */
.search-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 340px;
}

.search-box .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 14px;
}

.search-box .form-control {
    padding: 10px 16px 10px 40px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    font-size: 13px;
    background: #fafbfc;
    transition: all 0.2s ease;
    height: 44px;
}

.search-box .form-control:focus {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
    background: #ffffff;
}

.search-box .form-control::placeholder {
    color: #a0aec0;
    font-size: 13px;
}

/* Tabel */
.table-keluarga {
    font-size: 13px;
    margin: 0;
    width: 100%;
}

.table-keluarga thead th {
    background: #f8f9fc;
    color: #4a5568;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 12px 14px;
    border-bottom: 2px solid #edf2f7;
    white-space: nowrap;
}

.table-keluarga thead th i {
    margin-right: 4px;
    color: #8a94a6;
}

.table-keluarga tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}

.table-keluarga tbody tr:hover {
    background: #fafbfc;
}

.table-keluarga tbody tr:last-child td {
    border-bottom: none;
}

/* Info Keluarga */
.info-keluarga .nama-kk {
    font-weight: 600;
    color: #1a2634;
    font-size: 14px;
}

.info-keluarga .no-kk {
    font-size: 12px;
    color: #8a94a6;
}

/* Info Orang Tua */
.info-ortu {
    font-size: 13px;
    color: #4a5568;
}

.info-ortu .label {
    color: #8a94a6;
    font-weight: 500;
}

/* Info Alamat */
.info-alamat {
    font-size: 13px;
    color: #4a5568;
}

.info-alamat .detail {
    font-size: 12px;
    color: #8a94a6;
    margin-top: 2px;
}

/* Badge Anak */
.badge-anak {
    background: #e8f0fe;
    color: #2c6b9e;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-anak i {
    margin-right: 4px;
}

/* Aksi Buttons */
.btn-action {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    font-size: 13px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-action.edit {
    background: #e8f0fe;
    color: #2c6b9e;
}

.btn-action.edit:hover {
    background: #2c6b9e;
    color: #ffffff;
}

.btn-action.delete {
    background: #fef2f2;
    color: #dc2626;
}

.btn-action.delete:hover {
    background: #dc2626;
    color: #ffffff;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 12px;
    display: block;
}

.empty-state h6 {
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state p {
    color: #8a94a6;
    font-size: 13px;
}

/* Responsive */
@media (max-width: 768px) {
    .keluarga-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
    }
    
    .keluarga-header .header-right {
        justify-content: space-between;
    }
    
    .search-box {
        max-width: 100%;
    }
    
    .stat-mini {
        padding: 6px 12px;
    }
    
    .stat-mini .stat-number {
        font-size: 15px;
    }
    
    .table-keluarga {
        font-size: 12px;
    }
    
    .table-keluarga thead th,
    .table-keluarga tbody td {
        padding: 8px 10px;
    }
    
    .btn-tambah {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .keluarga-header .header-right {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stat-mini {
        justify-content: center;
    }
}


/* Modal Import */
.custom-file-input:focus ~ .custom-file-label {
    border-color: #2c6b9e;
    box-shadow: 0 0 0 3px rgba(44, 107, 158, 0.1);
}

.custom-file-input:focus ~ .custom-file-label::after {
    border-color: #2c6b9e;
}

.alert-info-import {
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 10px;
    padding: 16px 20px;
}

.alert-info-import i {
    color: #2c6b9e;
}

.alert-info-import ol {
    margin-top: 6px;
    padding-left: 20px;
    font-size: 13px;
    color: #4a5568;
    line-height: 1.8;
}

</style>

<div class="keluarga-container">

    <!-- ==========================================
    HEADER
    ========================================== -->
    <div class="keluarga-header">
        <div class="header-left">
            <h4>
                <i class="fas fa-users"></i>
                Data Keluarga
            </h4>
            <div class="sub-title">
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                Manajemen data keluarga terdaftar di Posyandu Bougenvil Belik
            </div>
        </div>
        <div class="header-right">
            <!-- Stat Mini -->
            <div class="stat-mini">
                <div class="stat-icon primary">
                    <i class="fas fa-home"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_keluarga ?></div>
                    <div class="stat-label">Keluarga</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-icon success">
                    <i class="fas fa-child"></i>
                </div>
                <div>
                    <div class="stat-number"><?= $total_anak ?></div>
                    <div class="stat-label">Total Anak</div>
                </div>
            </div>
            
            <!-- Tombol Tambah -->
            <button type="button" class="btn-tambah open-modal"
                    data-title="Tambah Keluarga Baru"
                    data-url="index.php?url=keluarga-create">
                <i class="fas fa-plus-circle"></i>
                Tambah Keluarga
            </button>

            <!-- Tombol Import -->
            <button type="button" class="btn-tambah" style="background: #28a745;" data-toggle="modal" data-target="#importModal">
                <i class="fas fa-file-upload"></i>
                Import Excel
            </button>
        </div>
    </div>

    <!-- ==========================================
    TABLE CARD
    ========================================== -->
    <div class="card-keluarga">
        <div class="card-body">

            <!-- Search -->
            <div class="search-wrapper">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchInput"
                           placeholder="Cari nama KK, No KK, atau alamat...">
                </div>
                <span style="font-size: 12px; color: #8a94a6;">
                    <i class="fas fa-database"></i> <?= $total_keluarga ?> data
                </span>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-keluarga">
                    <thead>
                        <tr>
                            <th width="45">#</th>
                            <th><i class="fas fa-user-friends"></i> Keluarga</th>
                            <th><i class="fas fa-user"></i> Orang Tua</th>
                            <th><i class="fas fa-map-marker-alt"></i> Alamat</th>
                            <th width="80"><i class="fas fa-child"></i> Anak</th>
                            <th width="110"><i class="fas fa-phone"></i> HP</th>
                            <th width="100" class="text-center"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (count($data) > 0): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($data as $d): ?>
                            <tr>
                                <td>
                                    <span style="font-weight: 600; color: #8a94a6; font-size: 12px;"><?= $no++ ?></span>
                                </td>
                                <td>
                                    <div class="info-keluarga">
                                        <div class="nama-kk"><?= htmlspecialchars($d['nama_kepala_keluarga'] ?? '-') ?></div>
                                        <div class="no-kk">KK: <?= htmlspecialchars($d['no_kk'] ?? '-') ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="info-ortu">
                                        <div><span class="label">Ayah:</span> <?= htmlspecialchars($d['nama_ayah'] ?? '-') ?></div>
                                        <div><span class="label">Ibu:</span> <?= htmlspecialchars($d['nama_ibu'] ?? '-') ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="info-alamat">
                                        <?= htmlspecialchars($d['alamat'] ?? '-') ?>
                                        <div class="detail">
                                            RT <?= htmlspecialchars($d['rt'] ?? '-') ?> / RW <?= htmlspecialchars($d['rw'] ?? '-') ?>
                                            · <?= htmlspecialchars($d['desa'] ?? '-') ?>, <?= htmlspecialchars($d['kecamatan'] ?? '-') ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-anak">
                                        <i class="fas fa-child"></i>
                                        <?= $d['jumlah_anak'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($d['no_hp'])): ?>
                                        <i class="fas fa-phone" style="color: #28a745; margin-right: 4px;"></i>
                                        <?= htmlspecialchars($d['no_hp']) ?>
                                    <?php else: ?>
                                        <span style="color: #d1d5db; font-size: 12px;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2" style="gap: 6px;">
                                        <!-- Edit -->
                                        <button type="button" class="btn-action edit open-modal"
                                                data-title="Edit Keluarga - <?= htmlspecialchars($d['nama_kepala_keluarga']) ?>"
                                                data-url="index.php?url=keluarga-edit&id=<?= $d['id'] ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Delete -->
                                        <a href="index.php?url=keluarga-delete&id=<?= $d['id'] ?>"
                                           class="btn-action delete"
                                           onclick="return confirm('Yakin ingin menghapus data keluarga <?= htmlspecialchars($d['nama_kepala_keluarga']) ?>?')"
                                           title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h6>Belum Ada Data Keluarga</h6>
                                        <p>Klik tombol "Tambah Keluarga" untuk menambahkan data baru</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<!-- ==========================================
MODAL GLOBAL
========================================== -->
<div class="modal fade" id="globalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden; border: none;">
            <div class="modal-header" style="background: #2c6b9e; color: #ffffff; border: none; padding: 16px 24px;">
                <h5 class="modal-title" id="modalTitle" style="font-weight: 600; font-size: 16px;">
                    <i class="fas fa-pen"></i> Modal
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <iframe id="modalFrame" loading="lazy" style="width:100%; min-height:650px; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>





<!-- ==========================================
MODAL IMPORT EXCEL
========================================== -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <!-- Header - Biru -->
            <div class="modal-header" style="background: #2c6b9e; color: #ffffff; border: none; padding: 18px 24px;">
                <h5 class="modal-title" style="font-weight: 600; font-size: 17px;">
                    <i class="fas fa-file-upload" style="margin-right: 10px;"></i> Import Data Keluarga dari Excel
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Body -->
            <div class="modal-body" style="padding: 28px 30px; background: #ffffff;">
                
                <!-- Info Box -->
                <div class="alert" style="border-radius: 10px; border: 1px solid #dbeafe; background: #eff6ff; color: #1a2634; padding: 16px 20px;">
                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                        <i class="fas fa-info-circle" style="color: #2c6b9e; font-size: 20px; margin-top: 2px;"></i>
                        <div>
                            <strong style="color: #2c6b9e;">Panduan Import Excel:</strong>
                            <ol style="margin-top: 8px; padding-left: 20px; font-size: 13px; color: #4a5568; line-height: 1.8;">
                                <li>Download template Excel di bawah ini</li>
                                <li>Isi data sesuai kolom yang tersedia <strong>(jangan ubah struktur kolom)</strong></li>
                                <li>Upload file Excel yang sudah diisi</li>
                                <li>Data akan otomatis ditambahkan ke sistem</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Tombol Download Template -->
                <div class="text-center mb-4" style="padding: 16px 0;">
                    <a href="index.php?url=keluarga-download" 
                    class="btn" 
                    style="background: #28a745; color: #ffffff; border-radius: 10px; padding: 12px 35px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px;">
                        <i class="fas fa-download"></i> Download Template Excel
                    </a>
                    <div style="font-size: 12px; color: #8a94a6; margin-top: 6px;">
                        <i class="fas fa-file-excel" style="color: #28a745;"></i> Format .xlsx
                    </div>
                </div>

                <hr style="border-color: #edf2f7; margin: 16px 0 24px 0;">

             
               <!-- Form Upload -->
                <form action="index.php?url=keluarga-import" method="POST" enctype="multipart/form-data" id="importForm">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="font-weight: 600; color: #4a5568; font-size: 14px; margin-bottom: 6px;">
                            <i class="fas fa-file-upload" style="color: #2c6b9e; margin-right: 6px;"></i> Pilih File Excel
                        </label>
                        
                        <div style="display: flex; align-items: center; gap: 12px; background: #fafbfc; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px 12px;">
                            <input type="file" name="file_excel" id="fileExcel" accept=".xlsx,.xls,.csv" required 
                                style="position: absolute; width: 0.1px; height: 0.1px; opacity: 0; overflow: hidden; z-index: -1;"
                                onchange="document.getElementById('fileName').innerHTML = '📊 ' + this.files[0].name; document.getElementById('fileName').style.color='#1a2634'; document.getElementById('fileName').style.fontWeight='500';">
                            <label for="fileExcel" style="background: #2c6b9e; color: white; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 13px; margin: 0; white-space: nowrap;">
                                <i class="fas fa-folder-open"></i> Pilih File
                            </label>
                            <span id="fileName" style="color: #4a5568; font-size: 13px; flex: 1; padding: 6px 0;">Belum ada file dipilih</span>
                        </div>
                        <small class="text-muted" style="font-size: 12px; display: block; margin-top: 6px;">
                            <i class="fas fa-info-circle"></i> Format yang didukung: .xlsx, .xls, .csv (Maks 2MB)
                        </small>
                    </div>

                    <div class="text-right mt-3" style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #edf2f7; padding-top: 20px;">
                        <button type="button" class="btn" data-dismiss="modal" style="background: #f0f4f8; color: #4a5568; border-radius: 8px; padding: 10px 28px; font-weight: 500; border: none; transition: all 0.2s ease;">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" name="import" class="btn" style="background: #28a745; color: #ffffff; border-radius: 8px; padding: 10px 28px; font-weight: 600; border: none; transition: all 0.3s ease;">
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
SCRIPT
========================================== -->
<script>
// ============================================================
// SEARCH
// ============================================================
const searchInput = document.getElementById("searchInput");
if (searchInput) {
    searchInput.addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#tableBody tr");
        rows.forEach(function(row) {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
}

// ============================================================
// OPEN MODAL
// ============================================================
document.querySelectorAll(".open-modal").forEach(function(button) {
    button.addEventListener("click", function() {
        let title = this.dataset.title;
        let url = this.dataset.url;
        document.getElementById("modalTitle").innerHTML = '<i class="fas fa-pen"></i> ' + title;
        document.getElementById("modalFrame").src = url;
        $("#globalModal").modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});

// Reset iframe saat modal ditutup
$('#globalModal').on('hidden.bs.modal', function() {
    document.getElementById("modalFrame").src = "";
});

// ============================================================
// SHOW FILE NAME ON SELECT - PAKAI EVENT DELEGATION
// ============================================================
document.addEventListener('change', function(e) {
    if (e.target && e.target.id === 'fileExcel') {
        var fileNameSpan = document.getElementById('fileName');
        if (e.target.files.length > 0) {
            fileNameSpan.textContent = '📊 ' + e.target.files[0].name;
            fileNameSpan.style.color = '#1a2634'; // ← UBAH KE HITAM
            fileNameSpan.style.fontWeight = '500';
        } else {
            fileNameSpan.textContent = 'Belum ada file dipilih';
            fileNameSpan.style.color = '';
            fileNameSpan.style.fontWeight = '';
        }
    }
});

// ============================================================
// RESET NAMA FILE SAAT MODAL DITUTUP
// ============================================================
$('#importModal').on('hidden.bs.modal', function() {
    var fileNameSpan = document.getElementById('fileName');
    if (fileNameSpan) {
        fileNameSpan.textContent = 'Belum ada file dipilih';
        fileNameSpan.style.color = '';
        fileNameSpan.style.fontWeight = '';
    }
    // Reset input file juga
    var fileInput = document.getElementById('fileExcel');
    if (fileInput) {
        fileInput.value = '';
    }
});
</script>