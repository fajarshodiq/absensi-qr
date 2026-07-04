<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"><b>Generate Laporan Kelas
                                <?= $kelas['kelas']; ?></b></h4>
                        <p class="card-category">Pilih bulan untuk mendownload laporan presensi</p>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">
                        <form action="<?= base_url('teacher/laporan/generate'); ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <input type="hidden" name="filter_type" id="filter_type" value="bulanan">
                            
                            <!-- Tab Selectors -->
                            <ul class="nav nav-pills nav-pills-primary justify-content-start mb-3" role="tablist" style="background: #f8f9fa; padding: 6px; border-radius: 6px;">
                                <li class="nav-item w-33 text-center">
                                    <a class="nav-link active py-2" data-toggle="pill" href="#tab-bulanan" role="tab" onclick="document.getElementById('filter_type').value = 'bulanan'" style="font-weight: 500; font-size: 13px;">Bulanan</a>
                                </li>
                                <li class="nav-item w-33 text-center">
                                    <a class="nav-link py-2" data-toggle="pill" href="#tab-mingguan" role="tab" onclick="document.getElementById('filter_type').value = 'mingguan'" style="font-weight: 500; font-size: 13px;">Mingguan</a>
                                </li>
                                <li class="nav-item w-33 text-center">
                                    <a class="nav-link py-2" data-toggle="pill" href="#tab-semester" role="tab" onclick="document.getElementById('filter_type').value = 'semester'" style="font-weight: 500; font-size: 13px;">Semester</a>
                                </li>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content my-3">
                                <!-- Bulanan Tab -->
                                <div class="tab-pane active" id="tab-bulanan" role="tabpanel">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group bmd-form-group is-filled">
                                                <label class="bmd-label-static font-weight-bold">Pilih Bulan</label>
                                                <select name="bulan_select" class="custom-select form-control" style="font-size: 14px;">
                                                    <?php 
                                                    $indoMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    $currentMonth = (int)date('m');
                                                    foreach ($indoMonths as $idx => $mName): 
                                                        $val = sprintf("%02d", $idx + 1);
                                                    ?>
                                                        <option value="<?= $val; ?>" <?= $val == $currentMonth ? 'selected' : ''; ?>><?= $mName; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group bmd-form-group is-filled">
                                                <label class="bmd-label-static font-weight-bold">Pilih Tahun</label>
                                                <select name="tahun_select" class="custom-select form-control" style="font-size: 14px;">
                                                    <?php 
                                                    $currentYear = (int)date('Y');
                                                    for($y = $currentYear - 4; $y <= $currentYear + 1; $y++): 
                                                    ?>
                                                        <option value="<?= $y; ?>" <?= $y == $currentYear ? 'selected' : ''; ?>><?= $y; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Mingguan Tab -->
                                <div class="tab-pane" id="tab-mingguan" role="tabpanel">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group bmd-form-group is-filled">
                                                <label class="bmd-label-static font-weight-bold">Tanggal Mulai</label>
                                                <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d', strtotime('-7 days')); ?>" style="font-size: 14px;">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group bmd-form-group is-filled">
                                                <label class="bmd-label-static font-weight-bold">Tanggal Selesai</label>
                                                <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d'); ?>" style="font-size: 14px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Semester Tab -->
                                <div class="tab-pane" id="tab-semester" role="tabpanel">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group bmd-form-group is-filled">
                                                <label class="bmd-label-static font-weight-bold">Semester</label>
                                                <select name="semester" class="custom-select form-control" style="font-size: 14px;">
                                                    <option value="ganjil">Ganjil (Juli - Des)</option>
                                                    <option value="genap">Genap (Jan - Juni)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group bmd-form-group is-filled">
                                                <label class="bmd-label-static font-weight-bold">Tahun Ajaran</label>
                                                <select name="tahun_ajaran" class="custom-select form-control" style="font-size: 14px;">
                                                    <?php 
                                                    for($y = $currentYear - 3; $y <= $currentYear + 1; $y++): 
                                                        $ta = "$y/" . ($y + 1);
                                                    ?>
                                                        <option value="<?= $ta; ?>" <?= $y == ($currentYear - 1) ? 'selected' : ''; ?>><?= $ta; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row align-items-center mt-4">
                                <div class="col-md-6">
                                    <div class="form-group bmd-form-group is-filled">
                                        <label class="bmd-label-static font-weight-bold">Format Unduhan</label>
                                        <select class="custom-select form-control" name="type" id="type" style="font-size: 14px;">
                                            <option value="pdf">PDF / Print</option>
                                            <option value="doc">Word Document (.doc)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-primary px-4 py-3" style="border-radius: 6px; box-shadow: none; font-size: 14px;">
                                        <i class="material-icons align-middle mr-1">download</i> Generate & Unduh
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>