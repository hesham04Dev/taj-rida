<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>بطاقات النقاط - جامع الرضا</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
    }

    body {
      background-color: #f0fdf4;
      padding: 20px;
    }

    .controls {
      text-align: center;
      margin-bottom: 25px;
    }

    .print-btn {
      background: linear-gradient(135deg, #059669, #84cc16);
      color: white;
      border: none;
      padding: 12px 35px;
      font-size: 1.1rem;
      font-weight: 700;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(132, 204, 22, 0.4);
      transition: transform 0.2s;
      margin-left: 12px;
    }

    .back-btn {
      background: #64748b;
      color: white;
      border: none;
      padding: 12px 25px;
      font-size: 1rem;
      font-weight: 700;
      border-radius: 10px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: transform 0.2s;
    }

    .print-btn:hover, .back-btn:hover { transform: scale(1.05); }

    .page-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: #064e3b;
      margin-bottom: 8px;
    }

    .cards-container {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
      width: max-content;
      margin: 0 auto;
    }

    .card-wrapper {
      position: relative;
      width: 320px;
      height: 190px;
      border-radius: 14px;
      padding: 2.5px;
      background: linear-gradient(135deg, #a3e635, #10b981, #06b6d4, #65a30d, #a3e635);
      box-shadow:
        0 8px 20px rgba(0, 0, 0, 0.08),
        0 0 15px rgba(132, 204, 22, 0.3),
        0 0 15px rgba(16, 185, 129, 0.2);
      break-inside: avoid !important;
      page-break-inside: avoid !important;
      contain: layout paint;
    }

    .card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      background-color: #ffffff;
      border-radius: 12px;
      padding: 14px 18px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
    }

    .card-pattern {
      position: absolute;
      inset: 0;
      opacity: 0.02;
      background-image: repeating-linear-gradient(
        -45deg,
        #000 0,
        #fff 2px,
        transparent 0,
        transparent 10px
      );
      pointer-events: none;
    }

    .card-watermark {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-size: 8.8rem;
      font-weight: 900;
      color: rgba(6, 78, 59, 0.06);
      pointer-events: none;
      line-height: 0.9;
      text-align: center;
      width: 100%;
      height: 100%;
      transform: rotate(-10deg);
      transform-origin: center center;
      z-index: 1;
    }

    .card-watermark span { display: block; white-space: nowrap; }

    .card-header {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .gov-name {
      color: #64748b;
      font-size: 0.6rem;
      font-weight: 700;
    }

    .card-main {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: baseline;
      gap: 6px;
      margin-top: 2px;
    }

    .points-number {
      font-size: 3.4rem;
      font-weight: 900;
      line-height: 1;
      background: linear-gradient(135deg, #059669, #65a30d, #0284c7);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      letter-spacing: -1.5px;
    }

    .points-label {
      font-size: 0.9rem;
      font-weight: 900;
      color: #4d7c0f;
    }

    .card-footer {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .student-details {
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    .student-name {
      color: #064e3b;
      font-size: 1.05rem;
      font-weight: 800;
    }

    .school-name {
      color: #64748b;
      font-size: 0.65rem;
      font-weight: 400;
      min-height: 10px;
    }

    .id-badge {
      background-color: #064e3b;
      color: #a3e635;
      font-weight: 900;
      font-size: 0.7rem;
      padding: 4px 10px;
      border-radius: 6px;
      letter-spacing: 1px;
      box-shadow: 0 3px 8px rgba(6, 78, 59, 0.25);
    }

    @media print {
      @page { size: A4 portrait; margin: 10mm; }

      body { background-color: #ffffff; padding: 0; }

      .controls { display: none !important; }

      .card-wrapper,
      .card-inner,
      .card-watermark {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        will-change: transform;
      }

      .card-wrapper { box-shadow: none; }
    }
  </style>
</head>
<body>

  <div class="controls">
    <a href="{{ url()->previous() }}" class="back-btn">← رجوع</a>
    <button class="print-btn" onclick="window.print()">🖨️ طباعة البطاقات (10 في الصفحة)</button>
    <p class="page-title" style="margin-top: 12px;">
      {{ $teacherName ? "بطاقات نقاط: {$teacherName}" : 'بطاقات نقاط جميع الطلاب' }}
      &nbsp;—&nbsp; {{ $students->count() }} طالب
    </p>
  </div>

  <div class="cards-container">
    @foreach($students as $student)
      <div class="card-wrapper">
        <div class="card-inner">
          <div class="card-pattern"></div>
          <div class="card-watermark">
            <span>صيف</span>
            <span>2026</span>
          </div>

          <div class="card-header">
            <span class="gov-name">جامع الرضا</span>
            <span class="gov-name">إدارة المعهد الشرعي</span>
          </div>

          <div class="card-main">
            <span class="points-number">{{ number_format($student->total_pts) }}</span>
            <span class="points-label">نقطة</span>
          </div>

          <div class="card-footer">
            <div class="student-details">
              <div class="student-name">{{ $student->name }}</div>
              <div class="school-name">{{ $student->teacher?->name }}</div>
            </div>
            <div class="id-badge">{{ str_pad($student->teacher_id, 2, '0', STR_PAD_LEFT) }}</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

</body>
</html>
