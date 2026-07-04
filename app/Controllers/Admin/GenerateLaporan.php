<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use DateTime;
use DateInterval;
use DatePeriod;

use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\PresensiGuruModel;
use App\Models\SiswaModel;
use App\Models\PresensiSiswaModel;

class GenerateLaporan extends BaseController
{
   protected SiswaModel $siswaModel;
   protected KelasModel $kelasModel;

   protected GuruModel $guruModel;

   protected PresensiSiswaModel $presensiSiswaModel;
   protected PresensiGuruModel $presensiGuruModel;

   public function __construct()
   {
      $this->siswaModel = new SiswaModel();
      $this->kelasModel = new KelasModel();

      $this->guruModel = new GuruModel();

      $this->presensiSiswaModel = new PresensiSiswaModel();
      $this->presensiGuruModel = new PresensiGuruModel();
   }

   public function index()
   {
      $kelas = $this->kelasModel->getDataKelas();
      $guru = $this->guruModel->getAllGuru();

      $siswaPerKelas = [];

      foreach ($kelas as $value) {
         array_push($siswaPerKelas, $this->siswaModel->getSiswaByKelas($value['id_kelas']));
      }

      $data = [
         'title' => 'Generate Laporan',
         'ctx' => 'laporan',
         'siswaPerKelas' => $siswaPerKelas,
         'kelas' => $kelas,
         'guru' => $guru
      ];

      return view('admin/generate-laporan/generate-laporan', $data);
   }

   public function generateLaporanSiswa()
   {
      $idKelas = $this->request->getVar('kelas');
      $siswa = $this->siswaModel->getSiswaByKelas($idKelas);
      $type = $this->request->getVar('type');

      if (empty($siswa)) {
         session()->setFlashdata([
            'msg' => 'Data siswa kosong!',
            'error' => true
         ]);
         return redirect()->to('/admin/laporan');
      }

      $kelas = (array) $this->kelasModel->getKelas($idKelas);

      // Determine date range based on filter type
      $filterType = $this->request->getVar('filter_type') ?? 'bulanan';
      $startDate = null;
      $endDate = null;
      $labelPeriode = '';

      if ($filterType == 'mingguan') {
         $startDate = $this->request->getVar('start_date') ?? date('Y-m-d');
         $endDate = $this->request->getVar('end_date') ?? date('Y-m-d');
         
         $startTI = new Time($startDate, locale: 'id');
         $endTI = new Time($endDate, locale: 'id');
         $labelPeriode = "Rentang: " . $startTI->toLocalizedString('d MMMM Y') . " s/d " . $endTI->toLocalizedString('d MMMM Y');
      } else if ($filterType == 'semester') {
         $semester = $this->request->getVar('semester') ?? 'ganjil';
         $tahunAjaran = $this->request->getVar('tahun_ajaran') ?? date('Y') . '/' . (date('Y') + 1);
         $years = explode('/', $tahunAjaran);
         
         if (count($years) == 2) {
            if ($semester == 'ganjil') {
               $startDate = $years[0] . "-07-01";
               $endDate = $years[0] . "-12-31";
               $labelPeriode = "Semester Ganjil TA " . $tahunAjaran;
            } else {
               $startDate = $years[1] . "-01-01";
               $endDate = $years[1] . "-06-30";
               $labelPeriode = "Semester Genap TA " . $tahunAjaran;
            }
         } else {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $labelPeriode = "Bulanan: " . (new Time($startDate, locale: 'id'))->toLocalizedString('MMMM Y');
         }
      } else {
         // bulanan
         $bulanInput = $this->request->getVar('bulanSiswa');
         $tahunInput = $this->request->getVar('tahunSiswa');
         if ($bulanInput && $tahunInput) {
            $bulan = $tahunInput . '-' . sprintf("%02d", $bulanInput);
         } else {
            $bulan = $this->request->getVar('tanggalSiswa') ?? date('Y-m');
         }
         
         $begin = new Time($bulan, locale: 'id');
         $startDate = $begin->format('Y-m-01');
         $endDate = $begin->format('Y-m-t');
         $labelPeriode = "Bulan: " . $begin->toLocalizedString('MMMM Y');
      }

      $begin = new Time($startDate, locale: 'id');
      $end = (new DateTime($endDate))->modify('+1 day');
      $interval = DateInterval::createFromDateString('1 day');
      $period = new DatePeriod($begin, $interval, $end);

      // Fetch all attendance for this class in the date range (OPTIMIZED SINGLE QUERY)
      $allPresensi = $this->presensiSiswaModel->db->table('tb_presensi_siswa')
         ->select('tb_presensi_siswa.*, tb_kehadiran.kehadiran')
         ->join('tb_kehadiran', 'tb_presensi_siswa.id_kehadiran = tb_kehadiran.id_kehadiran', 'left')
         ->where('tb_presensi_siswa.id_kelas', $idKelas)
         ->where('tb_presensi_siswa.tanggal >=', $startDate)
         ->where('tb_presensi_siswa.tanggal <=', $endDate)
         ->get()
         ->getResultArray();

      // Index attendance by student ID and date
      $presensiIndexed = [];
      foreach ($allPresensi as $p) {
         $presensiIndexed[$p['id_siswa']][$p['tanggal']] = $p;
      }

      $arrayTanggal = [];
      $dataAbsen = [];

      foreach ($period as $value) {
         $dateStr = $value->format('Y-m-d');
         // kecualikan hari sabtu dan minggu
         if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
            $lewat = Time::parse($dateStr)->isAfter(Time::today());

            $absenByTanggal = [];
            foreach ($siswa as $s) {
               $idSiswa = $s['id_siswa'];
               if (isset($presensiIndexed[$idSiswa][$dateStr])) {
                  $p = $presensiIndexed[$idSiswa][$dateStr];
                  $absenByTanggal[] = [
                     'id_presensi' => $p['id_presensi'],
                     'id_siswa' => $p['id_siswa'],
                     'tanggal' => $p['tanggal'],
                     'jam_masuk' => $p['jam_masuk'],
                     'jam_keluar' => $p['jam_keluar'],
                     'id_kehadiran' => $p['id_kehadiran'],
                     'keterangan' => $p['keterangan'],
                     'kehadiran' => $p['kehadiran']
                  ];
               } else {
                  $absenByTanggal[] = [
                     'id_presensi' => null,
                     'id_siswa' => $idSiswa,
                     'tanggal' => $dateStr,
                     'jam_masuk' => null,
                     'jam_keluar' => null,
                     'id_kehadiran' => null,
                     'keterangan' => '',
                     'kehadiran' => null
                  ];
               }
            }

            $absenByTanggal['lewat'] = $lewat;

            array_push($dataAbsen, $absenByTanggal);
            array_push($arrayTanggal, Time::createFromInstance($value, locale: 'id'));
         }
      }

      $laki = 0;
      foreach ($siswa as $value) {
         if ($value['jenis_kelamin'] != 'Perempuan') {
            $laki++;
         }
      }

      $data = [
         'tanggal' => $arrayTanggal,
         'bulan' => $labelPeriode,
         'listAbsen' => $dataAbsen,
         'listSiswa' => $siswa,
         'rekapSiswa' => [
            'laki' => $laki,
            'perempuan' => count($siswa) - $laki
         ],
         'kelas' => $kelas,
         'grup' => "kelas " . $kelas['kelas'],
      ];

      if ($type == 'doc') {
         $this->response->setHeader('Content-type', 'application/vnd.ms-word');
         $this->response->setHeader(
            'Content-Disposition',
            'attachment;Filename=laporan_absen_' . $kelas['kelas'] . '_' . str_replace(' ', '_', $labelPeriode) . '.doc'
         );

         return view('admin/generate-laporan/laporan-siswa', $data);
      }

      return view('admin/generate-laporan/laporan-siswa', $data) . view('admin/generate-laporan/topdf');
   }

   public function generateLaporanGuru()
   {
      $guru = $this->guruModel->getAllGuru();
      $type = $this->request->getVar('type');

      if (empty($guru)) {
         session()->setFlashdata([
            'msg' => 'Data guru kosong!',
            'error' => true
         ]);
         return redirect()->to('/admin/laporan');
      }

      // Determine date range based on filter type
      $filterType = $this->request->getVar('filter_type') ?? 'bulanan';
      $startDate = null;
      $endDate = null;
      $labelPeriode = '';

      if ($filterType == 'mingguan') {
         $startDate = $this->request->getVar('start_date') ?? date('Y-m-d');
         $endDate = $this->request->getVar('end_date') ?? date('Y-m-d');
         
         $startTI = new Time($startDate, locale: 'id');
         $endTI = new Time($endDate, locale: 'id');
         $labelPeriode = "Rentang: " . $startTI->toLocalizedString('d MMMM Y') . " s/d " . $endTI->toLocalizedString('d MMMM Y');
      } else if ($filterType == 'semester') {
         $semester = $this->request->getVar('semester') ?? 'ganjil';
         $tahunAjaran = $this->request->getVar('tahun_ajaran') ?? date('Y') . '/' . (date('Y') + 1);
         $years = explode('/', $tahunAjaran);
         
         if (count($years) == 2) {
            if ($semester == 'ganjil') {
               $startDate = $years[0] . "-07-01";
               $endDate = $years[0] . "-12-31";
               $labelPeriode = "Semester Ganjil TA " . $tahunAjaran;
            } else {
               $startDate = $years[1] . "-01-01";
               $endDate = $years[1] . "-06-30";
               $labelPeriode = "Semester Genap TA " . $tahunAjaran;
            }
         } else {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $labelPeriode = "Bulanan: " . (new Time($startDate, locale: 'id'))->toLocalizedString('MMMM Y');
         }
      } else {
         // bulanan
         $bulanInput = $this->request->getVar('bulanGuru');
         $tahunInput = $this->request->getVar('tahunGuru');
         if ($bulanInput && $tahunInput) {
            $bulan = $tahunInput . '-' . sprintf("%02d", $bulanInput);
         } else {
            $bulan = $this->request->getVar('tanggalGuru') ?? date('Y-m');
         }
         
         $begin = new Time($bulan, locale: 'id');
         $startDate = $begin->format('Y-m-01');
         $endDate = $begin->format('Y-m-t');
         $labelPeriode = "Bulan: " . $begin->toLocalizedString('MMMM Y');
      }

      $begin = new Time($startDate, locale: 'id');
      $end = (new DateTime($endDate))->modify('+1 day');
      $interval = DateInterval::createFromDateString('1 day');
      $period = new DatePeriod($begin, $interval, $end);

      // Fetch all attendance for teachers in the date range (OPTIMIZED SINGLE QUERY)
      $allPresensi = $this->presensiGuruModel->db->table('tb_presensi_guru')
         ->select('tb_presensi_guru.*, tb_kehadiran.kehadiran')
         ->join('tb_kehadiran', 'tb_presensi_guru.id_kehadiran = tb_kehadiran.id_kehadiran', 'left')
         ->where('tb_presensi_guru.tanggal >=', $startDate)
         ->where('tb_presensi_guru.tanggal <=', $endDate)
         ->get()
         ->getResultArray();

      // Index attendance by teacher ID and date
      $presensiIndexed = [];
      foreach ($allPresensi as $p) {
         $presensiIndexed[$p['id_guru']][$p['tanggal']] = $p;
      }

      $arrayTanggal = [];
      $dataAbsen = [];

      foreach ($period as $value) {
         $dateStr = $value->format('Y-m-d');
         // kecualikan hari sabtu dan minggu
         if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
            $lewat = Time::parse($dateStr)->isAfter(Time::today());

            $absenByTanggal = [];
            foreach ($guru as $g) {
               $idGuru = $g['id_guru'];
               if (isset($presensiIndexed[$idGuru][$dateStr])) {
                  $p = $presensiIndexed[$idGuru][$dateStr];
                  $absenByTanggal[] = [
                     'id_presensi' => $p['id_presensi'],
                     'id_guru' => $p['id_guru'],
                     'tanggal' => $p['tanggal'],
                     'jam_masuk' => $p['jam_masuk'],
                     'jam_keluar' => $p['jam_keluar'],
                     'id_kehadiran' => $p['id_kehadiran'],
                     'keterangan' => $p['keterangan'],
                     'kehadiran' => $p['kehadiran']
                  ];
               } else {
                  $absenByTanggal[] = [
                     'id_presensi' => null,
                     'id_guru' => $idGuru,
                     'tanggal' => $dateStr,
                     'jam_masuk' => null,
                     'jam_keluar' => null,
                     'id_kehadiran' => null,
                     'keterangan' => '',
                     'kehadiran' => null
                  ];
               }
            }

            $absenByTanggal['lewat'] = $lewat;

            array_push($dataAbsen, $absenByTanggal);
            array_push($arrayTanggal, Time::createFromInstance($value, locale: 'id'));
         }
      }

      $laki = 0;
      foreach ($guru as $value) {
         if ($value['jenis_kelamin'] != 'Perempuan') {
            $laki++;
         }
      }

      $data = [
         'tanggal' => $arrayTanggal,
         'bulan' => $labelPeriode,
         'listAbsen' => $dataAbsen,
         'listGuru' => $guru,
         'jumlahGuru' => [
            'laki' => $laki,
            'perempuan' => count($guru) - $laki
         ],
         'grup' => 'guru',
      ];

      if ($type == 'doc') {
         $this->response->setHeader('Content-type', 'application/vnd.ms-word');
         $this->response->setHeader(
            'Content-Disposition',
            'attachment;Filename=laporan_absen_guru_' . str_replace(' ', '_', $labelPeriode) . '.doc'
         );

         return view('admin/generate-laporan/laporan-guru', $data);
      }

      return view('admin/generate-laporan/laporan-guru', $data) . view('admin/generate-laporan/topdf');
   }
}
