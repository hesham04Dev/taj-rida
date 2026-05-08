<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المتابعة - {{ $date }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;700;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        vintage: {
                            gold: '#926c15',
                            ink: '#2d2d2d',
                            sepia: '#5e4b3c',
                        }
                    },
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style type="text/css">
        @media print {
            .no-print {
                display: none;
            }

            /* body {
                padding: 60px;
                margin: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            } */

            /* @page {
                margin: 0;*/
            /* Let the background image handle the margin */
            /* } */

            .page-break {
                page-break-after: always;
            }
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f1ea;
            /* Fallback color */
            background-image: url('{{ asset("assets/paper.jpeg") }}');
            background-size: cover;
            /* background-attachment: fixed; */
            background-repeat: repeat;
            /* -webkit-print-color-adjust: exact; */
            /* print-color-adjust: exact; */
            /* min-height: 100vh; */
            padding: 0 80px;
            /* Adjusted to stay inside the paper's frame */
        }

        /* Removing standard table borders to let the paper shine */
        table {
            background: transparent !important;
        }

        /* Subtle dividers instead of boxes */
        .row-divider {
            border-bottom: 1px solid rgba(94, 75, 60, 0.2);
        }

        thead {
            display: table-header-group;
        }
    </style>
</head>

<body class="text-vintage-ink">

    <div class="max-w-4xl mx-auto">
        <table class="w-full text-right border-collapse bg-transparent">
            <thead>
                <tr>
                    <th colspan="2" class="p-0 border-none">
                        <div class="flex justify-between items-end border-b-2 border-vintage-gold pb-6 mb-10">
                            <div>
                                <h1 class="text-4xl font-black text-vintage-ink tracking-tight"
                                    style="padding-top:75px">تقرير متابعة الحفظ</h1>

                            </div>
                            <div class="">
                                <div class="text-[10px] font-black">التاريخ
                                </div>
                                <div class="text-xl font-bold text-vintage-gold">{{ $date }}</div>
                            </div>
                        </div>
                    </th>
                </tr>

                <tr class="text-vintage-sepia border-b border-vintage-gold/30">
                    <th class="py-4 px-2 text-lg font-black">اسم الطالب</th>
                    <th class="py-4 px-2 text-lg font-black text-center">المقرر </th>
                </tr>
            </thead>

            <tbody class="bg-transparent">
                @foreach($groups as $group)
                    <tr class="row-divider">
                        <td class="py-8 px-2 align-top w-1/3">
                            <div class="font-black text-2xl text-vintage-ink">{{ $group['student']->name }}</div>

                        </td>

                        <td class="py-8 px-2 space-y-6">

                            {{-- Memorization Row --}}
                            @if(count($group['memorization']) > 0)
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="h-2 w-2 rounded-full bg-orange-700"></div>
                                        <span class="text-xs font-black text-orange-800 uppercase tracking-widest">حفظ</span>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($group['memorization'] as $sura)
                                            <div class="bg-orange-900/5 border border-orange-200/50 px-4 py-2 rounded-md">
                                                <span class="text-md font-bold text-vintage-ink">{{ $sura['name'] }}</span>
                                                @if(isset($sura['need_from_page']))
                                                    <div class="text-[11px] text-orange-800 font-bold">
                                                        من ص {{ $sura['need_from_page'] }} إلى {{ $sura['need_to_page'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Revision Row --}}
                            @if(count($group['revision']) > 0)
                                <div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="h-2 w-2 rounded-full bg-vintage-gold"></div>
                                        <span
                                            class="text-xs font-black text-vintage-sepia uppercase tracking-widest">مراجعة</span>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($group['revision'] as $sura)
                                            <div class="bg-vintage-gold/5 border border-vintage-gold/20 px-4 py-2 rounded-md">
                                                <span class="text-md font-bold text-vintage-ink">{{ $sura['name'] }}</span>
                                                @if(isset($sura['need_from_page']))
                                                    <div class="text-[11px] text-vintage-gold font-bold">
                                                        من ص {{ $sura['need_from_page'] }} إلى {{ $sura['need_to_page'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="2"
                        class="pt-12 pb-4 text-center text-[11px] text-vintage-sepia/60 font-bold tracking-widest">

                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        window.onload = function () {
            setTimeout(() => {
                window.print();
            }, 800);
        };
    </script>
</body>

</html>