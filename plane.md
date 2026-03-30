وثيقة تطوير تطبيق "تاج الرضا" (إدارة نقاط الطلاب)
1. الهدف العام

تطبيق  لإدارة حلقات التحفيظ أو الفصول الدراسية، يركز على سرعة الإدخال 
2. المواصفات التقنية (Tech Stack)

    Framework: laravel with filament and livewire (TALL STACK).
 إضافة طوابع زمنية (created_at, updated_at) لكل السجلات.

3. هيكلة البيانات (Data Schema)

    Teacher: (id, name, info, timestamps, email, pass,  role).

    Student: (id, name, age, grade, family_info, multiplier [default 1.0], father name,father phone, timestamps, more_details, note).

    Attendance: (id, student_id, date, status).

    Recitation/Revision: (id, student_id, date, sura_id,from,to, grade [default: Good]). note the to should be less or equal to the sura last aya

    sura (id, name , ayas_count)

    Recitation_pages/Revision_pages: (id, student_id, date, count,). note the count is float with one decimal point eg:3.5

    PointTransaction: (id, student_id, amount, reason, timestamp). السجل الرقمي.

    Settings: (id, key, value). should have by default recition_points_per_page , revision_points_per_page , attendance_points, absence_points

4. خريطة الصفحات والتفاعل (UI/UX Flow)
أ. الصفحة الرئيسية (اللوحة التفاعلية)

    Header:  محرك بحث ذكي + تاريخ اليوم + اسم الأستاذ الحالي مع اسم الحلقة.

    Student List (Quick Actions): كل طالب في Card يحتوي على:

        يمين: زر تبديل (حاضر/غائب) بلمسة واحدة.

        منتصف: اسم الطالب + إجمالي نقاطه (الرقمية).

        عدادات سريعة: زر + و - لعدد صفحات (التسميع) و (المراجعة).

        يسار: زر "منح نقاط" يفتح Popup سريع بختيارات (5, 10... 500) مع زر تأكيد.

        إضافة: زر خاص لخصم النقاط.

ب. صفحة ملف الطالب (Detailed View)

    عرض كافة بيانات الطالب الشخصية.

    قسم الشكاوى والملاحظات: زر + يفتح نافذة لكتابة شكوى مع تقييم من 10، وتظهر كقائمة زمنية بالأسفل.

ج. صفحة الإعدادات (Control Panel)

    قسم النقاط: تعديل قيم الحضور، الغياب، التسميع، والمراجعة.

    البيانات الشخصية: رابط لصفحة "تعديل بيانات الأستاذ".


5. منطق العمليات (Business Logic)

    حساب النقاط:

        عند زيادة صفحة تسميع: Points = Settings.recitation_base * Student.multiplier.
     او المراجعة ايضا
