<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <?php if (session()->getFlashdata('msg')): ?>
               <div class="pb-2 px-3">
                  <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success' ?> ">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                     </button>
                     <?= session()->getFlashdata('msg') ?>
                  </div>
               </div>
            <?php endif; ?>
            <div class="card">
               <div class="card-header card-header-tabs card-header-info">
                  <div class="nav-tabs-navigation">
                     <div class="row">
                        <div class="col">
                           <h4 class="card-title"><b>Generate Laporan</b></h4>
                           <p class="card-category">Laporan absen</p>
                        </div>
                     </div>
                  </div>
               </div>
                <div class="card-body">
                   <div class="row">
                      <!-- SECTION LAPORAN SISWA -->
                      <div class="col-md-6 mb-4">
                         <div class="card h-100 shadow-sm border" style="border-radius: 8px;">
                            <form action="<?= base_url('admin/laporan/siswa'); ?>" method="post" class="card-body d-flex flex-column" style="padding: 1.5rem;">
                               <h4 class="text-primary mb-3"><b><i class="material-icons align-middle mr-1">face</i> Laporan Absen Siswa</b></h4>
                               
                               <input type="hidden" name="filter_type" id="filter_type_siswa" value="bulanan">
                               
                               <!-- Tab Selectors -->
                               <ul class="nav nav-pills nav-pills-info justify-content-start mb-3" role="tablist" style="background: #f8f9fa; padding: 6px; border-radius: 6px;">
                                  <li class="nav-item w-33 text-center">
                                     <a class="nav-link active py-2" data-toggle="pill" href="#tab-siswa-bulanan" role="tab" onclick="document.getElementById('filter_type_siswa').value = 'bulanan'" style="font-weight: 500; font-size: 13px;">Bulanan</a>
                                  </li>
                                  <li class="nav-item w-33 text-center">
                                     <a class="nav-link py-2" data-toggle="pill" href="#tab-siswa-mingguan" role="tab" onclick="document.getElementById('filter_type_siswa').value = 'mingguan'" style="font-weight: 500; font-size: 13px;">Mingguan</a>
                                  </li>
                                  <li class="nav-item w-33 text-center">
                                     <a class="nav-link py-2" data-toggle="pill" href="#tab-siswa-semester" role="tab" onclick="document.getElementById('filter_type_siswa').value = 'semester'" style="font-weight: 500; font-size: 13px;">Semester</a>
                                  </li>
                               </ul>
                               
                               <!-- Tab Content -->
                               <div class="tab-content my-3">
                                  <!-- Bulanan Tab -->
                                  <div class="tab-pane active" id="tab-siswa-bulanan" role="tabpanel">
                                     <div class="row">
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Pilih Bulan</label>
                                           <select name="bulanSiswa" class="custom-select form-control" style="font-size: 14px;">
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
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Pilih Tahun</label>
                                           <select name="tahunSiswa" class="custom-select form-control" style="font-size: 14px;">
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
                                  
                                  <!-- Mingguan Tab -->
                                  <div class="tab-pane" id="tab-siswa-mingguan" role="tabpanel">
                                     <div class="row">
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Tanggal Mulai</label>
                                           <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d', strtotime('-7 days')); ?>" style="font-size: 14px;">
                                        </div>
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Tanggal Selesai</label>
                                           <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d'); ?>" style="font-size: 14px;">
                                        </div>
                                     </div>
                                  </div>
                                  
                                  <!-- Semester Tab -->
                                  <div class="tab-pane" id="tab-siswa-semester" role="tabpanel">
                                     <div class="row">
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Semester</label>
                                           <select name="semester" class="custom-select form-control" style="font-size: 14px;">
                                              <option value="ganjil">Ganjil (Juli - Des)</option>
                                              <option value="genap">Genap (Jan - Juni)</option>
                                           </select>
                                        </div>
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Tahun Ajaran</label>
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

                               <div class="form-group my-3">
                                  <label class="text-secondary font-weight-bold" style="font-size: 12px; margin-bottom: 5px;">Pilih Kelas</label>
                                  <select name="kelas" class="custom-select form-control" required style="font-size: 14px;">
                                     <option value="">-- Pilih Kelas --</option>
                                     <?php foreach ($kelas as $key => $value): ?>
                                        <?php
                                        $idKelas = $value['id_kelas'];
                                        $namaKelas = $value['kelas'];
                                        $totalSiswa = count($siswaPerKelas[$key]);
                                        ?>
                                        <option value="<?= $idKelas; ?>">
                                           <?= "$namaKelas - {$totalSiswa} siswa"; ?>
                                        </option>
                                     <?php endforeach; ?>
                                  </select>
                               </div>
                               
                               <div class="errMsg"></div>
                               
                               <div class="mt-auto pt-3 d-flex flex-column">
                                  <button type="submit" name="type" value="pdf" class="btn btn-danger btn-block pl-3 mb-2" style="border-radius: 6px; box-shadow: none;">
                                     <div class="row align-items-center">
                                        <div class="col-auto">
                                           <i class="material-icons" style="font-size: 24px;">picture_as_pdf</i>
                                        </div>
                                        <div class="col text-left">
                                           <span style="font-size: 14px; font-weight: bold; text-transform: uppercase;">Unduh PDF / Print</span>
                                        </div>
                                     </div>
                                  </button>
                                  <button type="submit" name="type" value="doc" class="btn btn-info btn-block pl-3 m-0" style="border-radius: 6px; box-shadow: none;">
                                     <div class="row align-items-center">
                                        <div class="col-auto">
                                           <i class="material-icons" style="font-size: 24px;">description</i>
                                        </div>
                                        <div class="col text-left">
                                           <span style="font-size: 14px; font-weight: bold; text-transform: uppercase;">Unduh Word (.doc)</span>
                                        </div>
                                     </div>
                                  </button>
                               </div>
                            </form>
                         </div>
                      </div>
                      
                      <!-- SECTION LAPORAN GURU -->
                      <div class="col-md-6 mb-4">
                         <div class="card h-100 shadow-sm border" style="border-radius: 8px;">
                            <form action="<?= base_url('admin/laporan/guru'); ?>" method="post" class="card-body d-flex flex-column" style="padding: 1.5rem;">
                               <h4 class="text-success mb-3"><b><i class="material-icons align-middle mr-1">school</i> Laporan Absen Guru</b></h4>
                               <p class="text-muted" style="font-size: 13px;">Total jumlah guru: <strong class="text-dark"><?= count($guru); ?> orang</strong></p>
                               
                               <input type="hidden" name="filter_type" id="filter_type_guru" value="bulanan">
                               
                               <!-- Tab Selectors -->
                               <ul class="nav nav-pills nav-pills-success justify-content-start mb-3" role="tablist" style="background: #f8f9fa; padding: 6px; border-radius: 6px;">
                                  <li class="nav-item w-33 text-center">
                                     <a class="nav-link active py-2" data-toggle="pill" href="#tab-guru-bulanan" role="tab" onclick="document.getElementById('filter_type_guru').value = 'bulanan'" style="font-weight: 500; font-size: 13px;">Bulanan</a>
                                  </li>
                                  <li class="nav-item w-33 text-center">
                                     <a class="nav-link py-2" data-toggle="pill" href="#tab-guru-mingguan" role="tab" onclick="document.getElementById('filter_type_guru').value = 'mingguan'" style="font-weight: 500; font-size: 13px;">Mingguan</a>
                                  </li>
                                  <li class="nav-item w-33 text-center">
                                     <a class="nav-link py-2" data-toggle="pill" href="#tab-guru-semester" role="tab" onclick="document.getElementById('filter_type_guru').value = 'semester'" style="font-weight: 500; font-size: 13px;">Semester</a>
                                  </li>
                                </ul>
                                
                               <!-- Tab Content -->
                               <div class="tab-content my-3">
                                  <!-- Bulanan Tab -->
                                  <div class="tab-pane active" id="tab-guru-bulanan" role="tabpanel">
                                     <div class="row">
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Pilih Bulan</label>
                                           <select name="bulanGuru" class="custom-select form-control" style="font-size: 14px;">
                                              <?php 
                                              foreach ($indoMonths as $idx => $mName): 
                                                 $val = sprintf("%02d", $idx + 1);
                                              ?>
                                                 <option value="<?= $val; ?>" <?= $val == $currentMonth ? 'selected' : ''; ?>><?= $mName; ?></option>
                                              <?php endforeach; ?>
                                           </select>
                                        </div>
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Pilih Tahun</label>
                                           <select name="tahunGuru" class="custom-select form-control" style="font-size: 14px;">
                                              <?php 
                                              for($y = $currentYear - 4; $y <= $currentYear + 1; $y++): 
                                              ?>
                                                 <option value="<?= $y; ?>" <?= $y == $currentYear ? 'selected' : ''; ?>><?= $y; ?></option>
                                              <?php endfor; ?>
                                           </select>
                                        </div>
                                     </div>
                                  </div>
                                  
                                  <!-- Mingguan Tab -->
                                  <div class="tab-pane" id="tab-guru-mingguan" role="tabpanel">
                                     <div class="row">
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Tanggal Mulai</label>
                                           <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d', strtotime('-7 days')); ?>" style="font-size: 14px;">
                                        </div>
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Tanggal Selesai</label>
                                           <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d'); ?>" style="font-size: 14px;">
                                        </div>
                                     </div>
                                  </div>
                                  
                                  <!-- Semester Tab -->
                                  <div class="tab-pane" id="tab-guru-semester" role="tabpanel">
                                     <div class="row">
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Semester</label>
                                           <select name="semester" class="custom-select form-control" style="font-size: 14px;">
                                              <option value="ganjil">Ganjil (Juli - Des)</option>
                                              <option value="genap">Genap (Jan - Juni)</option>
                                           </select>
                                        </div>
                                        <div class="col-6">
                                           <label class="text-secondary font-weight-bold" style="font-size: 12px;">Tahun Ajaran</label>
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
                               
                               <div class="mt-auto pt-3 d-flex flex-column">
                                  <button type="submit" name="type" value="pdf" class="btn btn-danger btn-block pl-3 mb-2" style="border-radius: 6px; box-shadow: none;">
                                     <div class="row align-items-center">
                                        <div class="col-auto">
                                           <i class="material-icons" style="font-size: 24px;">picture_as_pdf</i>
                                        </div>
                                        <div class="col text-left">
                                           <span style="font-size: 14px; font-weight: bold; text-transform: uppercase;">Unduh PDF / Print</span>
                                        </div>
                                     </div>
                                  </button>
                                  <button type="submit" name="type" value="doc" class="btn btn-info btn-block pl-3 m-0" style="border-radius: 6px; box-shadow: none;">
                                     <div class="row align-items-center">
                                        <div class="col-auto">
                                           <i class="material-icons" style="font-size: 24px;">description</i>
                                        </div>
                                        <div class="col text-left">
                                           <span style="font-size: 14px; font-weight: bold; text-transform: uppercase;">Unduh Word (.doc)</span>
                                        </div>
                                     </div>
                                  </button>
                               </div>
                            </form>
                         </div>
                      </div>
                   </div>
                </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?= $this->endSection() ?>