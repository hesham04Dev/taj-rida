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
                            <div class="flex items-end gap-3" style="padding-top:75px">
                                <img src="{{ asset("assets/reda.png") }}" width="40px" class="">
                                <h1 class="text-4xl font-normal text-vintage-ink tracking-tight">
                                    قائمة مهام الدرس
                                </h1>


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
                    <th class="py-4 px-2 text-lg font-black ">المقرر </th>
                </tr>
            </thead>

            <tbody class="bg-transparent">
                @php
                    $i = 0;
                @endphp
                @foreach($groups as $group)
                    @php
                        $i++;
                    @endphp
                    <tr class="row-divider">
                        <td class="py-4 px-2 align-top w-1/4">
                            <div class="text-xl text-vintage-ink font-bold">{{ $i }}. {{ $group['student']->name }}</div>

                        </td>

                        <td class="py-4 px-2 space-y-6">

                            {{-- Memorization Row --}}
                            @if(count($group['memorization']) > 0)
                                <div class="flex items-start gap-2">
                                    <div class="flex items-center gap-2 mb-3">

                                        <span class="text-s font-black text-orange-800 uppercase tracking-widest"
                                            style="width: 60px;">حفظ</span>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($group['memorization'] as $sura)
                                            <div class="bg-orange-900/5 border border-orange-200/50 px-4 py-1 rounded-md">
                                                <span class="text-lg font-bold text-vintage-ink">{{ $sura['name'] }}</span>
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
                                <div class="flex items-start gap-2">
                                    <div class="flex items-center gap-2 mb-3">

                                        <span class="text-s font-black text-vintage-sepia uppercase tracking-widest"
                                            style="width: 60px;">مراجعة</span>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($group['revision'] as $sura)
                                            <div class="bg-vintage-gold/5 border border-vintage-gold/20 px-4 py-1 rounded-md">
                                                <span class="text-lg font-bold text-vintage-ink">{{ $sura['name'] }}</span>
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