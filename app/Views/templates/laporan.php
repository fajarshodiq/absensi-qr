<html>

<head>
   <title>Rekap Absen <?= esc($grup) ?></title>
   <style>
      :root {
         --primary-color: #2c3e50;
         --border-color: #dee2e6;
         --text-dark: #333333;
         --text-muted: #6c757d;
         
         --bg-hadir: #e6f4ea;
         --color-hadir: #137333;
         
         --bg-sakit: #e8f0fe;
         --color-sakit: #1a73e8;
         
         --bg-izin: #fef7e0;
         --color-izin: #b06000;
         
         --bg-alpa: #fce8e6;
         --color-alpa: #c5221f;
      }

      body {
         font-family: 'Inter', Arial, Helvetica, sans-serif;
         color: var(--text-dark);
         margin: 1.5cm;
         background-color: #ffffff;
         font-size: 12px;
         line-height: 1.4;
      }

      /* Report Header */
      .report-header-table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 25px;
         border-bottom: 3px double var(--primary-color);
         padding-bottom: 15px;
      }
      .report-header-table td {
         border: none !important;
         padding: 5px 0;
      }
      .school-title {
         font-size: 20px;
         font-weight: 800;
         color: var(--primary-color);
         margin: 0 0 4px 0;
         text-transform: uppercase;
         letter-spacing: 0.5px;
      }
      .report-title {
         font-size: 15px;
         font-weight: 700;
         margin: 0 0 4px 0;
         letter-spacing: 0.5px;
      }
      .school-year {
         font-size: 12px;
         color: var(--text-muted);
         margin: 0;
         font-weight: 600;
      }

      /* Report Metadata */
      .meta-table {
         width: 100%;
         margin-bottom: 15px;
         font-weight: 600;
      }
      .meta-table td {
         border: none !important;
         padding: 2px 0;
         font-size: 12px;
      }

      /* Grid Table */
      .report-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 10px;
         font-size: 11px;
         box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      }
      .report-table th, .report-table td {
         border: 1px solid var(--border-color);
         padding: 8px 6px;
         text-align: center;
         vertical-align: middle;
      }
      .report-table th {
         background-color: #f1f3f5;
         color: var(--primary-color);
         font-weight: 700;
         text-transform: uppercase;
         font-size: 10px;
      }
      .report-table tr.student-row:nth-child(even) {
         background-color: #f8f9fa;
      }
      .report-table tr.student-row:hover {
         background-color: #f1f3f5;
      }
      .student-name-td {
         text-align: left !important;
         font-weight: 600;
         padding-left: 12px !important;
      }

      /* Attendance Badges (Soft Pastel Colors) */
      .status-cell {
         font-weight: 700;
         font-size: 11px;
         width: 25px;
      }
      .status-h {
         background-color: var(--bg-hadir) !important;
         color: var(--color-hadir) !important;
      }
      .status-s {
         background-color: var(--bg-sakit) !important;
         color: var(--color-sakit) !important;
      }
      .status-i {
         background-color: var(--bg-izin) !important;
         color: var(--color-izin) !important;
      }
      .status-a {
         background-color: var(--bg-alpa) !important;
         color: var(--color-alpa) !important;
      }
      .status-empty {
         background-color: #ffffff;
      }

      /* Totals styling */
      .total-col {
         font-weight: 700;
         font-size: 11px;
         width: 32px;
      }
      .total-h { background-color: rgba(22, 160, 133, 0.08) !important; color: #16a085; }
      .total-s { background-color: rgba(41, 128, 185, 0.08) !important; color: #2980b9; }
      .total-i { background-color: rgba(243, 156, 18, 0.08) !important; color: #f39c12; }
      .total-a { background-color: rgba(192, 41, 43, 0.08) !important; color: #c0392b; }

      /* Summary Box at the bottom */
      .summary-section {
         margin-top: 30px;
         width: 100%;
      }
      .summary-card {
         border: 1px solid var(--border-color);
         border-radius: 6px;
         padding: 15px;
         background-color: #fcfcfc;
         display: inline-block;
         min-width: 250px;
      }
      .summary-card h5 {
         margin: 0 0 10px 0;
         color: var(--primary-color);
         font-size: 13px;
         border-bottom: 1px solid var(--border-color);
         padding-bottom: 5px;
      }
      .summary-row {
         display: flex;
         justify-content: space-between;
         margin-bottom: 5px;
         font-size: 12px;
      }
      .summary-value {
         font-weight: 700;
      }

      /* Print and layout configuration */
      @media print {
         @page {
            size: A4 landscape;
            margin: 1cm 1.5cm;
         }
         body {
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
         }
         .report-table {
            box-shadow: none;
         }
      }
   </style>
</head>

<body>

   <?= $this->renderSection('content') ?>

</body>

</html>