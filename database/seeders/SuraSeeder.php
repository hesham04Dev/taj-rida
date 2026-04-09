<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sura;

class SuraSeeder extends Seeder
{
    public function run(): void
    {$suras = [
    ['id' => 1,  'name' => 'الفاتحة', 'ayas_count' => 7, 'from_page' => 1.0, 'to_page' => 2.0, 'pages_count' => 1.0],
    ['id' => 2,  'name' => 'البقرة', 'ayas_count' => 286, 'from_page' => 2.0, 'to_page' => 50.0, 'pages_count' => 48.0],
    ['id' => 3,  'name' => 'آل عمران', 'ayas_count' => 200, 'from_page' => 50.0, 'to_page' => 77.0, 'pages_count' => 27.0],
    ['id' => 4,  'name' => 'النساء', 'ayas_count' => 176, 'from_page' => 77.0, 'to_page' => 105.5, 'pages_count' => 28.5], // Shared p.106
    ['id' => 5,  'name' => 'المائدة', 'ayas_count' => 120, 'from_page' => 105.5, 'to_page' => 127.0, 'pages_count' => 21.5],
    ['id' => 6,  'name' => 'الأنعام', 'ayas_count' => 165, 'from_page' => 128.0, 'to_page' => 151.0, 'pages_count' => 23.0],
    ['id' => 7,  'name' => 'الأعراف', 'ayas_count' => 206, 'from_page' => 151.0, 'to_page' => 177.0, 'pages_count' => 26.0],
    ['id' => 8,  'name' => 'الأنفال', 'ayas_count' => 75, 'from_page' => 177.0, 'to_page' => 187.0, 'pages_count' => 10.0],
    ['id' => 9,  'name' => 'التوبة', 'ayas_count' => 129, 'from_page' => 187.0, 'to_page' => 208.0, 'pages_count' => 21.0],
    ['id' => 10, 'name' => 'يونس', 'ayas_count' => 109, 'from_page' => 208.0, 'to_page' => 220.5, 'pages_count' => 12.5], // Shared p.221
    ['id' => 11, 'name' => 'هود', 'ayas_count' => 123, 'from_page' => 220.5, 'to_page' => 234.5, 'pages_count' => 14.0], // Shared p.221 & 235
    ['id' => 12, 'name' => 'يوسف', 'ayas_count' => 111, 'from_page' => 234.5, 'to_page' => 249.0, 'pages_count' => 14.5], // Shared p.235
    ['id' => 13, 'name' => 'الرعد', 'ayas_count' => 43, 'from_page' => 249.0, 'to_page' => 254.5, 'pages_count' => 5.5], // Shared p.255
    ['id' => 14, 'name' => 'إبراهيم', 'ayas_count' => 52, 'from_page' => 254.5, 'to_page' => 262.0, 'pages_count' => 7.5],
    ['id' => 15, 'name' => 'الحجر', 'ayas_count' => 99, 'from_page' => 262.0, 'to_page' => 266.5, 'pages_count' => 4.5], // Shared p.267
    ['id' => 16, 'name' => 'النحل', 'ayas_count' => 128, 'from_page' => 266.5, 'to_page' => 282.0, 'pages_count' => 15.5],
    ['id' => 17, 'name' => 'الإسراء', 'ayas_count' => 111, 'from_page' => 282.0, 'to_page' => 292.5, 'pages_count' => 10.5], // Shared p.293
    ['id' => 18, 'name' => 'الكهف', 'ayas_count' => 110, 'from_page' => 292.5, 'to_page' => 305.0, 'pages_count' => 12.5],
    ['id' => 19, 'name' => 'مريم', 'ayas_count' => 98, 'from_page' => 305.0, 'to_page' => 311.5, 'pages_count' => 6.5], // Shared p.312
    ['id' => 20, 'name' => 'طه', 'ayas_count' => 135, 'from_page' => 311.5, 'to_page' => 322.0, 'pages_count' => 10.5],
    ['id' => 21, 'name' => 'الأنبياء', 'ayas_count' => 112, 'from_page' => 322.0, 'to_page' => 332.0, 'pages_count' => 10.0],
    ['id' => 22, 'name' => 'الحج', 'ayas_count' => 78, 'from_page' => 332.0, 'to_page' => 342.0, 'pages_count' => 10.0],
    ['id' => 23, 'name' => 'المؤمنون', 'ayas_count' => 118, 'from_page' => 342.0, 'to_page' => 350.0, 'pages_count' => 8.0],
    ['id' => 24, 'name' => 'النور', 'ayas_count' => 64, 'from_page' => 350.0, 'to_page' => 358.5, 'pages_count' => 8.5], // Shared p.359
    ['id' => 25, 'name' => 'الفرقان', 'ayas_count' => 77, 'from_page' => 358.5, 'to_page' => 367.0, 'pages_count' => 8.5],
    ['id' => 26, 'name' => 'الشعراء', 'ayas_count' => 227, 'from_page' => 367.0, 'to_page' => 377.0, 'pages_count' => 10.0],
    ['id' => 27, 'name' => 'النمل', 'ayas_count' => 93, 'from_page' => 377.0, 'to_page' => 384.5, 'pages_count' => 7.5], // Shared p.385
    ['id' => 28, 'name' => 'القصص', 'ayas_count' => 88, 'from_page' => 384.5, 'to_page' => 396.0, 'pages_count' => 11.5],
    ['id' => 29, 'name' => 'العنكبوت', 'ayas_count' => 69, 'from_page' => 396.0, 'to_page' => 404.0, 'pages_count' => 8.0],
    ['id' => 30, 'name' => 'الروم', 'ayas_count' => 60, 'from_page' => 404.0, 'to_page' => 411.0, 'pages_count' => 7.0],
    ['id' => 31, 'name' => 'لقمان', 'ayas_count' => 34, 'from_page' => 411.0, 'to_page' => 415.0, 'pages_count' => 4.0],
    ['id' => 32, 'name' => 'السجدة', 'ayas_count' => 30, 'from_page' => 415.0, 'to_page' => 418.0, 'pages_count' => 3.0],
    ['id' => 33, 'name' => 'الأحزاب', 'ayas_count' => 73, 'from_page' => 418.0, 'to_page' => 428.0, 'pages_count' => 10.0],
    ['id' => 34, 'name' => 'سبأ', 'ayas_count' => 54, 'from_page' => 428.0, 'to_page' => 433.5, 'pages_count' => 5.5], // Shared p.434
    ['id' => 35, 'name' => 'فاطر', 'ayas_count' => 45, 'from_page' => 433.5, 'to_page' => 439.5, 'pages_count' => 6.0], // Shared p.440
    ['id' => 36, 'name' => 'يس', 'ayas_count' => 83, 'from_page' => 439.5, 'to_page' => 446.0, 'pages_count' => 6.5],
    ['id' => 37, 'name' => 'الصافات', 'ayas_count' => 182, 'from_page' => 446.0, 'to_page' => 453.0, 'pages_count' => 7.0],
    ['id' => 38, 'name' => 'ص', 'ayas_count' => 88, 'from_page' => 453.0, 'to_page' => 457.5, 'pages_count' => 4.5], // Shared p.458
    ['id' => 39, 'name' => 'الزمر', 'ayas_count' => 75, 'from_page' => 457.5, 'to_page' => 466.5, 'pages_count' => 9.0], // Shared p.467
    ['id' => 40, 'name' => 'غافر', 'ayas_count' => 85, 'from_page' => 466.5, 'to_page' => 477.0, 'pages_count' => 10.5],
    ['id' => 41, 'name' => 'فصلت', 'ayas_count' => 54, 'from_page' => 477.0, 'to_page' => 483.0, 'pages_count' => 6.0],
    ['id' => 42, 'name' => 'الشورى', 'ayas_count' => 53, 'from_page' => 483.0, 'to_page' => 488.5, 'pages_count' => 5.5], // Shared p.489
    ['id' => 43, 'name' => 'الزخرف', 'ayas_count' => 89, 'from_page' => 488.5, 'to_page' => 496.0, 'pages_count' => 7.5],
    ['id' => 44, 'name' => 'الدخان', 'ayas_count' => 59, 'from_page' => 496.0, 'to_page' => 499.0, 'pages_count' => 3.0],
    ['id' => 45, 'name' => 'الجاثية', 'ayas_count' => 37, 'from_page' => 499.0, 'to_page' => 501.5, 'pages_count' => 2.5], // Shared p.502
    ['id' => 46, 'name' => 'الأحقاف', 'ayas_count' => 35, 'from_page' => 501.5, 'to_page' => 507.0, 'pages_count' => 5.5],
    ['id' => 47, 'name' => 'محمد', 'ayas_count' => 38, 'from_page' => 507.0, 'to_page' => 511.0, 'pages_count' => 4.0],
    ['id' => 48, 'name' => 'الفتح', 'ayas_count' => 29, 'from_page' => 511.0, 'to_page' => 514.5, 'pages_count' => 3.5], // Shared p.515
    ['id' => 49, 'name' => 'الحجرات', 'ayas_count' => 18, 'from_page' => 514.5, 'to_page' => 518.0, 'pages_count' => 3.5],
    ['id' => 50, 'name' => 'ق', 'ayas_count' => 45, 'from_page' => 518.0, 'to_page' => 519.5, 'pages_count' => 1.5], // Shared p.520
    ['id' => 51, 'name' => 'الذاريات', 'ayas_count' => 60, 'from_page' => 519.5, 'to_page' => 522.5, 'pages_count' => 3.0], // Shared p.523
    ['id' => 52, 'name' => 'الطور', 'ayas_count' => 49, 'from_page' => 522.5, 'to_page' => 526.0, 'pages_count' => 3.5],
    ['id' => 53, 'name' => 'النجم', 'ayas_count' => 62, 'from_page' => 526.0, 'to_page' => 527.5, 'pages_count' => 1.5], // Shared p.528
    ['id' => 54, 'name' => 'القمر', 'ayas_count' => 55, 'from_page' => 527.5, 'to_page' => 530.5, 'pages_count' => 3.0], // Shared p.531
    ['id' => 55, 'name' => 'الرحمن', 'ayas_count' => 78, 'from_page' => 530.5, 'to_page' => 533.5, 'pages_count' => 3.0], // Shared p.534
    ['id' => 56, 'name' => 'الواقعة', 'ayas_count' => 96, 'from_page' => 533.5, 'to_page' => 536.5, 'pages_count' => 3.0], // Shared p.537
    ['id' => 57, 'name' => 'الحديد', 'ayas_count' => 29, 'from_page' => 536.5, 'to_page' => 542.0, 'pages_count' => 5.5],
    ['id' => 58, 'name' => 'المجادلة', 'ayas_count' => 22, 'from_page' => 542.0, 'to_page' => 544.5, 'pages_count' => 2.5], // Shared p.545
    ['id' => 59, 'name' => 'الحشر', 'ayas_count' => 24, 'from_page' => 544.5, 'to_page' => 549.0, 'pages_count' => 4.5],
    ['id' => 60, 'name' => 'الممتحنة', 'ayas_count' => 13, 'from_page' => 549.0, 'to_page' => 550.5, 'pages_count' => 1.5], // Shared p.551
    ['id' => 61, 'name' => 'الصف', 'ayas_count' => 14, 'from_page' => 550.5, 'to_page' => 553.0, 'pages_count' => 2.5],
    ['id' => 62, 'name' => 'الجمعة', 'ayas_count' => 11, 'from_page' => 553.0, 'to_page' => 553.5, 'pages_count' => 0.5], // Shared p.554
    ['id' => 63, 'name' => 'المنافقون', 'ayas_count' => 11, 'from_page' => 553.5, 'to_page' => 556.0, 'pages_count' => 2.5],
    ['id' => 64, 'name' => 'التغابن', 'ayas_count' => 18, 'from_page' => 556.0, 'to_page' => 558.0, 'pages_count' => 2.0],
    ['id' => 65, 'name' => 'الطلاق', 'ayas_count' => 12, 'from_page' => 558.0, 'to_page' => 560.0, 'pages_count' => 2.0],
    ['id' => 66, 'name' => 'التحريم', 'ayas_count' => 12, 'from_page' => 560.0, 'to_page' => 562.0, 'pages_count' => 2.0],
    ['id' => 67, 'name' => 'الملك', 'ayas_count' => 30, 'from_page' => 562.0, 'to_page' => 563.5, 'pages_count' => 1.5], // Shared p.564
    ['id' => 68, 'name' => 'القلم', 'ayas_count' => 52, 'from_page' => 563.5, 'to_page' => 567.0, 'pages_count' => 3.5],
    ['id' => 69, 'name' => 'الحاقة', 'ayas_count' => 52, 'from_page' => 567.0, 'to_page' => 567.5, 'pages_count' => 0.5], // Shared p.568
    ['id' => 70, 'name' => 'المعارج', 'ayas_count' => 44, 'from_page' => 567.5, 'to_page' => 569.5, 'pages_count' => 2.0], // Shared p.570
    ['id' => 71, 'name' => 'نوح', 'ayas_count' => 28, 'from_page' => 569.5, 'to_page' => 572.0, 'pages_count' => 2.5],
    ['id' => 72, 'name' => 'الجن', 'ayas_count' => 28, 'from_page' => 572.0, 'to_page' => 574.0, 'pages_count' => 2.0],
    ['id' => 73, 'name' => 'المزمل', 'ayas_count' => 20, 'from_page' => 574.0, 'to_page' => 574.5, 'pages_count' => 0.5], // Shared p.575
    ['id' => 74, 'name' => 'المدثر', 'ayas_count' => 56, 'from_page' => 574.5, 'to_page' => 576.5, 'pages_count' => 2.0], // Shared p.577
    ['id' => 75, 'name' => 'القيامة', 'ayas_count' => 40, 'from_page' => 576.5, 'to_page' => 578.0, 'pages_count' => 1.5],
    ['id' => 76, 'name' => 'الإنسان', 'ayas_count' => 31, 'from_page' => 578.0, 'to_page' => 580.0, 'pages_count' => 2.0],
    ['id' => 77, 'name' => 'المرسلات', 'ayas_count' => 50, 'from_page' => 580.0, 'to_page' => 582.0, 'pages_count' => 2.0],
    ['id' => 78, 'name' => 'النبأ', 'ayas_count' => 40, 'from_page' => 582.0, 'to_page' => 583.0, 'pages_count' => 1.0],
    ['id' => 79, 'name' => 'النازعات', 'ayas_count' => 46, 'from_page' => 583.0, 'to_page' => 585.0, 'pages_count' => 2.0],
    ['id' => 80, 'name' => 'عبس', 'ayas_count' => 42, 'from_page' => 585.0, 'to_page' => 586.0, 'pages_count' => 1.0],
    ['id' => 81, 'name' => 'التكوير', 'ayas_count' => 29, 'from_page' => 586.0, 'to_page' => 587.0, 'pages_count' => 1.0],
    ['id' => 82, 'name' => 'الانفطار', 'ayas_count' => 19, 'from_page' => 587.0, 'to_page' => 587.5, 'pages_count' => 0.5], // Shared p.587
    ['id' => 83, 'name' => 'المطففين', 'ayas_count' => 36, 'from_page' => 587.5, 'to_page' => 589.0, 'pages_count' => 1.5],
    ['id' => 84, 'name' => 'الانشقاق', 'ayas_count' => 25, 'from_page' => 589.0, 'to_page' => 590.0, 'pages_count' => 1.0],
    ['id' => 85, 'name' => 'البروج', 'ayas_count' => 22, 'from_page' => 590.0, 'to_page' => 591.0, 'pages_count' => 1.0],
    ['id' => 86, 'name' => 'الطارق', 'ayas_count' => 17, 'from_page' => 591.0, 'to_page' => 591.5, 'pages_count' => 0.5], // Shared p.591
    ['id' => 87, 'name' => 'الأعلى', 'ayas_count' => 19, 'from_page' => 591.5, 'to_page' => 592.0, 'pages_count' => 0.5],
    ['id' => 88, 'name' => 'الغاشية', 'ayas_count' => 26, 'from_page' => 592.0, 'to_page' => 593.0, 'pages_count' => 1.0],
    ['id' => 89, 'name' => 'الفجر', 'ayas_count' => 30, 'from_page' => 593.0, 'to_page' => 594.0, 'pages_count' => 1.0],
    ['id' => 90, 'name' => 'البلد', 'ayas_count' => 20, 'from_page' => 594.0, 'to_page' => 595.0, 'pages_count' => 1.0],
    ['id' => 91, 'name' => 'الشمس', 'ayas_count' => 15, 'from_page' => 595.0, 'to_page' => 595.5, 'pages_count' => 0.5], // Shared p.595
    ['id' => 92, 'name' => 'الليل', 'ayas_count' => 21, 'from_page' => 595.5, 'to_page' => 596.0, 'pages_count' => 0.5],
    ['id' => 93, 'name' => 'الضحى', 'ayas_count' => 11, 'from_page' => 596.0, 'to_page' => 596.3, 'pages_count' => 0.3], // Shared p.596
    ['id' => 94, 'name' => 'الشرح', 'ayas_count' => 8, 'from_page' => 596.3, 'to_page' => 596.6, 'pages_count' => 0.3],
    ['id' => 95, 'name' => 'التين', 'ayas_count' => 8, 'from_page' => 596.6, 'to_page' => 597.0, 'pages_count' => 0.4],
    ['id' => 96, 'name' => 'العلق', 'ayas_count' => 19, 'from_page' => 597.0, 'to_page' => 598.0, 'pages_count' => 1.0],
    ['id' => 97, 'name' => 'القدر', 'ayas_count' => 5, 'from_page' => 598.0, 'to_page' => 598.4, 'pages_count' => 0.4], // Shared p.598
    ['id' => 98, 'name' => 'البينة', 'ayas_count' => 8, 'from_page' => 598.4, 'to_page' => 599.0, 'pages_count' => 0.6],
    ['id' => 99, 'name' => 'الزلزلة', 'ayas_count' => 8, 'from_page' => 599.0, 'to_page' => 599.4, 'pages_count' => 0.4], // Shared p.599
    ['id' => 100, 'name' => 'العاديات', 'ayas_count' => 11, 'from_page' => 599.4, 'to_page' => 600.0, 'pages_count' => 0.6],
    ['id' => 101, 'name' => 'القارعة', 'ayas_count' => 11, 'from_page' => 600.0, 'to_page' => 600.5, 'pages_count' => 0.5], // Shared p.600
    ['id' => 102, 'name' => 'التكاثر', 'ayas_count' => 8, 'from_page' => 600.5, 'to_page' => 601.0, 'pages_count' => 0.5],
    ['id' => 103, 'name' => 'العصر', 'ayas_count' => 3, 'from_page' => 601.0, 'to_page' => 601.3, 'pages_count' => 0.3], // Shared p.601
    ['id' => 104, 'name' => 'الهمزة', 'ayas_count' => 9, 'from_page' => 601.3, 'to_page' => 601.6, 'pages_count' => 0.3],
    ['id' => 105, 'name' => 'الفيل', 'ayas_count' => 5, 'from_page' => 601.6, 'to_page' => 602.0, 'pages_count' => 0.4],
    ['id' => 106, 'name' => 'قريش', 'ayas_count' => 4, 'from_page' => 602.0, 'to_page' => 602.3, 'pages_count' => 0.3], // Shared p.602
    ['id' => 107, 'name' => 'الماعون', 'ayas_count' => 7, 'from_page' => 602.3, 'to_page' => 602.6, 'pages_count' => 0.3],
    ['id' => 108, 'name' => 'الكوثر', 'ayas_count' => 3, 'from_page' => 602.6, 'to_page' => 603.0, 'pages_count' => 0.4],
    ['id' => 109, 'name' => 'الكافرون', 'ayas_count' => 6, 'from_page' => 603.0, 'to_page' => 603.3, 'pages_count' => 0.3], // Shared p.603
    ['id' => 110, 'name' => 'النصر', 'ayas_count' => 3, 'from_page' => 603.3, 'to_page' => 603.6, 'pages_count' => 0.3],
    ['id' => 111, 'name' => 'المسد', 'ayas_count' => 5, 'from_page' => 603.6, 'to_page' => 604.0, 'pages_count' => 0.4],
    ['id' => 112, 'name' => 'الإخلاص', 'ayas_count' => 4, 'from_page' => 604.0, 'to_page' => 604.3, 'pages_count' => 0.3], // Shared p.604
    ['id' => 113, 'name' => 'الفلق', 'ayas_count' => 5, 'from_page' => 604.3, 'to_page' => 604.6, 'pages_count' => 0.3],
    ['id' => 114, 'name' => 'الناس', 'ayas_count' => 6, 'from_page' => 604.6, 'to_page' => 605.0, 'pages_count' => 0.4],
];
        foreach ($suras as $sura) {
            Sura::updateOrCreate(['id' => $sura['id']], $sura);
        }
    }
}
