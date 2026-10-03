<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use Illuminate\Database\Seeder;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $juzData = $this->juzData();

        foreach ($juzData as $juz) {
            Curriculum::updateOrCreate(
                ['number' => $juz['number']],
                [
                    'name' => $juz['name'],
                    'children' => $juz['children'],
                ]
            );
        }
    }

    /**
     * Returns all 30 juz with their children.
     * Juz 1-29: children are integer pages.
     * Juz 30 (عمّ): children are full suras.
     *
     * @return array<int, array{number: int, name: string, children: array}>
     */
    private function juzData(): array
    {
        // Build page-based children for juz 1-29
        // Quran pages: 1-604 total (Uthmani 604-page Mushaf)
        // Juz boundaries (start page of each juz):
        $juzStartPages = [
            1 => 1,
            2 => 22,
            3 => 42,
            4 => 62,
            5 => 82,
            6 => 102,
            7 => 121,
            8 => 142,
            9 => 162,
            10 => 182,
            11 => 201,
            12 => 222,
            13 => 242,
            14 => 262,
            15 => 282,
            16 => 302,
            17 => 322,
            18 => 342,
            19 => 362,
            20 => 382,
            21 => 402,
            22 => 422,
            23 => 442,
            24 => 462,
            25 => 482,
            26 => 502,
            27 => 522,
            28 => 542,
            29 => 562,
            30 => 582,  // Juz Amma starts at page 582
        ];

        $juzNames = [
            1 => 'الجزء الأول ',
            2 => 'الجزء الثاني ',
            3 => 'الجزء الثالث ',
            4 => 'الجزء الرابع ',
            5 => 'الجزء الخامس ',
            6 => 'الجزء السادس ',
            7 => 'الجزء السابع ',
            8 => 'الجزء الثامن ',
            9 => 'الجزء التاسع ',
            10 => 'الجزء العاشر ',
            11 => 'الجزء الحادي عشر ',
            12 => 'الجزء الثاني عشر ',
            13 => 'الجزء الثالث عشر ',
            14 => 'الجزء الرابع عشر ',
            15 => 'الجزء الخامس عشر ',
            16 => 'الجزء السادس عشر ',
            17 => 'الجزء السابع عشر ',
            18 => 'الجزء الثامن عشر ',
            19 => 'الجزء التاسع عشر ',
            20 => 'الجزء العشرون ',
            21 => 'الجزء الحادي والعشرون ',
            22 => 'الجزء الثاني والعشرون ',
            23 => 'الجزء الثالث والعشرون ',
            24 => 'الجزء الرابع والعشرون ',
            25 => 'الجزء الخامس والعشرون ',
            26 => 'الجزء السادس والعشرون ',
            27 => 'الجزء السابع والعشرون ',
            28 => 'الجزء الثامن والعشرون ',
            29 => 'الجزء التاسع والعشرون ',
            30 => 'جزء عمّ',
        ];

        $juzList = [];

        // Juz 1-29: build integer page children
        for ($juzNum = 1; $juzNum <= 29; $juzNum++) {
            $startPage = $juzStartPages[$juzNum];
            $endPage = $juzStartPages[$juzNum + 1];

            $children = [];
            for ($page = $startPage; $page <= $endPage; $page++) {
                $children[] = [
                    'label' => "صفحة {$page}",
                    'from_page' => $page,
                    'to_page' => $page + 1,
                    'points_multiplier' => 1.0,
                ];
            }

            $juzList[] = [
                'number' => $juzNum,
                'name' => $juzNames[$juzNum],
                'children' => $children,
            ];
        }

        // Juz 30 (عمّ): children are full suras
        // Juz Amma starts at page 582 (النبأ) and ends at page 604 (الناس)
        // We also include الفاتحة as a common memorization target
        $juzAmmaChildren = [
            ['label' => 'الفاتحة', 'from_page' => 1, 'to_page' => 2, 'points_multiplier' => 1],
            ['label' => 'النبأ', 'from_page' => 582, 'to_page' => 584, 'points_multiplier' => 1.5],
            ['label' => 'النازعات', 'from_page' => 583, 'to_page' => 585, 'points_multiplier' => 1.5],
            ['label' => 'عبس', 'from_page' => 585, 'to_page' => 586, 'points_multiplier' => 1],
            ['label' => 'التكوير', 'from_page' => 586, 'to_page' => 587, 'points_multiplier' => 1],
            ['label' => 'الانفطار', 'from_page' => 587, 'to_page' => 587, 'points_multiplier' => 0.8],
            ['label' => 'المطففين', 'from_page' => 587, 'to_page' => 589, 'points_multiplier' => 1.2],
            ['label' => 'الانشقاق', 'from_page' => 589, 'to_page' => 590, 'points_multiplier' => 1],
            ['label' => 'البروج', 'from_page' => 590, 'to_page' => 591, 'points_multiplier' => 1],
            ['label' => 'الطارق', 'from_page' => 591, 'to_page' => 591, 'points_multiplier' => 0.5],
            ['label' => 'الأعلى', 'from_page' => 591, 'to_page' => 592, 'points_multiplier' => 0.5],
            ['label' => 'الغاشية', 'from_page' => 592, 'to_page' => 593, 'points_multiplier' => 1],
            ['label' => 'الفجر', 'from_page' => 593, 'to_page' => 594, 'points_multiplier' => 1],
            ['label' => 'البلد', 'from_page' => 594, 'to_page' => 595, 'points_multiplier' => 1],
            ['label' => 'الشمس', 'from_page' => 595, 'to_page' => 595, 'points_multiplier' => 0.5],
            ['label' => 'الليل', 'from_page' => 595, 'to_page' => 596, 'points_multiplier' => 0.5],
            ['label' => 'الضحى', 'from_page' => 596, 'to_page' => 596, 'points_multiplier' => 0.5],
            ['label' => 'الشرح', 'from_page' => 596, 'to_page' => 596, 'points_multiplier' => 0.5],
            ['label' => 'التين', 'from_page' => 596, 'to_page' => 597, 'points_multiplier' => 0.3],
            ['label' => 'العلق', 'from_page' => 597, 'to_page' => 598, 'points_multiplier' => 0.7],
            ['label' => 'القدر', 'from_page' => 598, 'to_page' => 598, 'points_multiplier' => 0.3],
            ['label' => 'البينة', 'from_page' => 598, 'to_page' => 599, 'points_multiplier' => 0.7],
            ['label' => 'الزلزلة', 'from_page' => 599, 'to_page' => 599, 'points_multiplier' => 0.4],
            ['label' => 'العاديات', 'from_page' => 599, 'to_page' => 600, 'points_multiplier' => 0.3],
            ['label' => 'القارعة', 'from_page' => 600, 'to_page' => 600, 'points_multiplier' => 0.4],
            ['label' => 'التكاثر', 'from_page' => 600, 'to_page' => 601, 'points_multiplier' => 0.3],
            ['label' => 'العصر', 'from_page' => 601, 'to_page' => 601, 'points_multiplier' => 0.3],
            ['label' => 'الهمزة', 'from_page' => 601, 'to_page' => 601, 'points_multiplier' => 0.4],
            ['label' => 'الفيل', 'from_page' => 601, 'to_page' => 602, 'points_multiplier' => 0.3],
            ['label' => 'قريش', 'from_page' => 602, 'to_page' => 602, 'points_multiplier' => 0.3],
            ['label' => 'الماعون', 'from_page' => 602, 'to_page' => 602, 'points_multiplier' => 0.4],
            ['label' => 'الكوثر', 'from_page' => 602, 'to_page' => 603, 'points_multiplier' => 0.3],
            ['label' => 'الكافرون', 'from_page' => 603, 'to_page' => 603, 'points_multiplier' => 0.3],
            ['label' => 'النصر', 'from_page' => 603, 'to_page' => 603, 'points_multiplier' => 0.3],
            ['label' => 'المسد', 'from_page' => 603, 'to_page' => 604, 'points_multiplier' => 0.3],
            ['label' => 'الإخلاص', 'from_page' => 604, 'to_page' => 604, 'points_multiplier' => 0.3],
            ['label' => 'الفلق', 'from_page' => 604, 'to_page' => 604, 'points_multiplier' => 0.3],
            ['label' => 'الناس', 'from_page' => 604, 'to_page' => 605, 'points_multiplier' => 0.3],
        ];

        $juzList[] = [
            'number' => 30,
            'name' => $juzNames[30],
            'children' => $juzAmmaChildren,
        ];

        return $juzList;
    }
}
