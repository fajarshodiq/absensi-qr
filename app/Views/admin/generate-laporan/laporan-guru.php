<?= $this->extend('templates/laporan') ?>

<?= $this->section('content') ?>

<!-- Header Laporan -->
<table class="report-header-table">
   <tr>
      <td width="120px" align="center">
         <?php if (getLogo()): ?>
            <img src="<?= getLogo(); ?>" width="80px" height="80px" style="object-fit: contain;"></img>
         <?php endif; ?>
      </td>
      <td align="center" style="text-align: center;">
         <h1 class="school-title"><?= esc($generalSettings->school_name); ?></h1>
         <h2 class="report-title">DAFTAR HADIR GURU</h2>
         <p class="school-year">TAHUN PELAJARAN <?= esc($generalSettings->school_year); ?></p>
      </td>
      <td width="120px"></td>
   </tr>
</table>

<!-- Metadata Laporan -->
<table class="meta-table">
   <tr>
      <td width="50%"><strong>Periode:</strong> <?= esc($bulan); ?></td>
      <td width="50%" align="right" style="text-align: right;"><strong>Grup:</strong> Guru / Staf Pengajar</td>
   </tr>
</table>

<!-- Main Attendance Table -->
<table class="report-table">
   <thead>
      <tr>
         <th rowspan="3" width="30px">No</th>
         <th rowspan="3" style="text-align: left; padding-left: 12px;">Nama Guru</th>
         <th colspan="<?= count($tanggal); ?>">Hari / Tanggal</th>
         <th colspan="4" rowspan="2">Total & %</th>
      </tr>
      <tr>
         <?php foreach ($tanggal as $value) : ?>
            <th style="font-size: 9px; font-weight: bold;"><?= $value->toLocalizedString('E'); ?></th>
         <?php endforeach; ?>
      </tr>
      <tr>
         <?php foreach ($tanggal as $value) : ?>
            <th><?= $value->format('d'); ?></th>
         <?php endforeach; ?>
         <th class="total-h" style="width: 35px;">H</th>
         <th class="total-s" style="width: 35px;">S</th>
         <th class="total-i" style="width: 35px;">I</th>
         <th class="total-a" style="width: 35px;">A</th>
      </tr>
   </thead>
   <tbody>
      <?php 
      $i = 0;
      $totalHadirAll = 0;
      $totalSakitAll = 0;
      $totalIzinAll = 0;
      $totalAlpaAll = 0;

      // Count elapsed active days
      $activeDays = count(array_filter($listAbsen, function($a) {
         return !$a['lewat'];
      }));

      foreach ($listGuru as $guru) : 
         $jumlahHadir = count(array_filter($listAbsen, function ($a) use ($i) {
            if ($a['lewat'] || is_null($a[$i]['id_kehadiran'])) return false;
            return $a[$i]['id_kehadiran'] == 1;
         }));
         $jumlahSakit = count(array_filter($listAbsen, function ($a) use ($i) {
            if ($a['lewat'] || is_null($a[$i]['id_kehadiran'])) return false;
            return $a[$i]['id_kehadiran'] == 2;
         }));
         $jumlahIzin = count(array_filter($listAbsen, function ($a) use ($i) {
            if ($a['lewat'] || is_null($a[$i]['id_kehadiran'])) return false;
            return $a[$i]['id_kehadiran'] == 3;
         }));
         $jumlahTidakHadir = count(array_filter($listAbsen, function ($a) use ($i) {
            if ($a['lewat']) return false;
            if (is_null($a[$i]['id_kehadiran']) || $a[$i]['id_kehadiran'] == 4) return true;
            return false;
         }));

         // Accumulate counters
         $totalHadirAll += $jumlahHadir;
         $totalSakitAll += $jumlahSakit;
         $totalIzinAll += $jumlahIzin;
         $totalAlpaAll += $jumlahTidakHadir;

         // Calculate individual percentages
         $persenHadir = $activeDays > 0 ? round(($jumlahHadir / $activeDays) * 100) : 0;
         $persenSakit = $activeDays > 0 ? round(($jumlahSakit / $activeDays) * 100) : 0;
         $persenIzin = $activeDays > 0 ? round(($jumlahIzin / $activeDays) * 100) : 0;
         $persenAlpa = $activeDays > 0 ? round(($jumlahTidakHadir / $activeDays) * 100) : 0;
      ?>
         <tr class="student-row">
            <td><?= $i + 1; ?></td>
            <td class="student-name-td"><?= esc($guru['nama_guru']); ?></td>
            <?php foreach ($listAbsen as $absen) : ?>
               <?= kehadiranCell($absen[$i]['id_kehadiran'] ?? ($absen['lewat'] ? 5 : 4)); ?>
            <?php endforeach; ?>
            <td class="total-col total-h">
               <?= $jumlahHadir != 0 ? $jumlahHadir : '-'; ?><br>
               <span class="pct-label"><?= $persenHadir ?>%</span>
            </td>
            <td class="total-col total-s">
               <?= $jumlahSakit != 0 ? $jumlahSakit : '-'; ?><br>
               <span class="pct-label"><?= $persenSakit ?>%</span>
            </td>
            <td class="total-col total-i">
               <?= $jumlahIzin != 0 ? $jumlahIzin : '-'; ?><br>
               <span class="pct-label"><?= $persenIzin ?>%</span>
            </td>
            <td class="total-col total-a">
               <?= $jumlahTidakHadir != 0 ? $jumlahTidakHadir : '-'; ?><br>
               <span class="pct-label"><?= $persenAlpa ?>%</span>
            </td>
         </tr>
      <?php
         $i++;
      endforeach; ?>
   </tbody>
</table>

<!-- Summary and Statistics Section -->
<table style="width: 100%; border: none; margin-top: 25px;">
   <tr style="border: none;">
      <td width="50%" style="vertical-align: top; border: none; padding: 0;">
         <div class="summary-card">
            <h5><b>Detail Data Guru</b></h5>
            <div class="summary-row">
               <span>Jumlah Guru</span>
               <span class="summary-value">: <?= count($listGuru); ?> orang</span>
            </div>
            <div class="summary-row">
               <span>Laki-laki</span>
               <span class="summary-value">: <?= $jumlahGuru['laki']; ?> orang</span>
            </div>
            <div class="summary-row">
               <span>Perempuan</span>
               <span class="summary-value">: <?= $jumlahGuru['perempuan']; ?> orang</span>
            </div>
         </div>
      </td>
      <td width="50%" style="vertical-align: top; border: none; padding: 0;" align="right">
         <?php
         $countGuru = count($listGuru);
         $totalPossibleDays = $countGuru * $activeDays;
         $overallHadir = $totalPossibleDays > 0 ? round(($totalHadirAll / $totalPossibleDays) * 100) : 0;
         $overallSakit = $totalPossibleDays > 0 ? round(($totalSakitAll / $totalPossibleDays) * 100) : 0;
         $overallIzin = $totalPossibleDays > 0 ? round(($totalIzinAll / $totalPossibleDays) * 100) : 0;
         $overallAlpa = $totalPossibleDays > 0 ? round(($totalAlpaAll / $totalPossibleDays) * 100) : 0;
         ?>
         <div class="summary-card" style="text-align: left;">
            <h5><b>Rata-rata Kehadiran Guru (<?= $activeDays; ?> Hari Aktif)</b></h5>
            <div class="summary-row">
               <span style="color: var(--color-hadir); font-weight: bold;">Hadir (H)</span>
               <span class="summary-value" style="color: var(--color-hadir);"><?= $overallHadir; ?>%</span>
            </div>
            <div class="summary-row">
               <span style="color: var(--color-sakit); font-weight: bold;">Sakit (S)</span>
               <span class="summary-value" style="color: var(--color-sakit);"><?= $overallSakit; ?>%</span>
            </div>
            <div class="summary-row">
               <span style="color: var(--color-izin); font-weight: bold;">Izin (I)</span>
               <span class="summary-value" style="color: var(--color-izin);"><?= $overallIzin; ?>%</span>
            </div>
            <div class="summary-row">
               <span style="color: var(--color-alpa); font-weight: bold;">Alpa (A)</span>
               <span class="summary-value" style="color: var(--color-alpa);"><?= $overallAlpa; ?>%</span>
            </div>
         </div>
      </td>
   </tr>
</table>

<?php
function kehadiranCell($kehadiran)
{
   switch ($kehadiran) {
      case 1:
         return "<td class='status-cell status-h'>H</td>";
      case 2:
         return "<td class='status-cell status-s'>S</td>";
      case 3:
         return "<td class='status-cell status-i'>I</td>";
      case 4:
         return "<td class='status-cell status-a'>A</td>";
      case 5:
      default:
         return "<td class='status-cell status-empty'></td>";
   }
}
?>
<?= $this->endSection() ?>