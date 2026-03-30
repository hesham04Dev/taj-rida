📄 وثيقة تطوير نظام "تاج الرضا" لإدارة حلقات التحفيظ (Laravel Filament)
1. نظرة عامة (Overview)

    الاسم: تاج الرضا.

    الهدف: نظام مركزي لإدارة نقاط الطلاب، الحضور، والتسميع، مع واجهة سريعة جداً للمُعلمين ولوحة تحكم شاملة للمدير.

    التقنيات: Laravel 11, Filament v3 (TALL Stack: Tailwind, Alpine.js, Laravel, Livewire).

    قاعدة البيانات: MySQL.

2. هيكلة قاعدة البيانات (Database Schema)
الجداول الأساسية:

    Users: (id, name, email, password, role [admin, teacher], phone, timestamps).

    Students: (id, teacher_id [FK], name, age, grade, father_name, father_phone, points_multiplier [default 1.0], more_details, note, timestamps).

    Suras: (id, name, ayas_count). بيانات ثابتة لـ 114 سورة.

    Recitations (التسميع): (id, student_id [FK], sura_id [FK], from_aya, to_aya, repetition_number, grade [default: Good], date).

    Revisions (المراجعة): (id, student_id [FK], sura_id [FK], from_aya, to_aya, repetition_number, grade [default: Good], date).

    PageLogs: (id, student_id [FK], type [recitation, revision], count [float: e.g. 1.5], date).

    Attendance: (id, student_id [FK], date, is_present [boolean]).

    PointTransactions: (id, student_id [FK], teacher_id [FK], amount [int], reason, created_at).

    StudentNotes (الشكاوى): (id, student_id [FK], description, rating [1-10], date).

    Settings: (id, key [unique], value). المفاتيح المطلوبة: recitation_points_per_page, revision_points_per_page, attendance_points, absence_penalty.

3. منطق العمليات (Business Logic)
أ. نظام النقاط التفاعلي:

    عند تسجيل صفحة تسميع (مثلاً 1.0) في PageLog:

        يتم جلب recitation_points_per_page من الإعدادات.

        العملية: Points = SettingsValue * Student->points_multiplier.

        يتم إنشاء سجل تلقائي في PointTransactions.

    نفس المنطق ينطبق على المراجعة و الحضور والغياب.

ب. الصلاحيات (Authorization):

    المدير (Admin): صلاحية كاملة على كل البيانات، إدارة الأساتذة، وتعديل الإعدادات العامة.

    الأستاذ (Teacher): يرى فقط الطلاب المرتبطين به (teacher_id). يتم تطبيق Global Scope على مودل Student.

4. تصميم الواجهات (UI/UX - Filament Resources)
أ. لوحة التحكم (Custom Dashboard):

    عرض الطلاب كـ Cards مخصصة باستخدام Livewire Component تحتوي على:

        Action Buttons:

            زر تبديل (Toggle) للحضور والغياب (أخضر/أحمر).

            أزرار + و - سريعة لزيادة/نقصان صفحات التسميع والمراجعة (بمقدار 0.5 أو 1.0).

            زر "منح نقاط" يفتح Modal بـ (Buttons) لقيم ثابتة (5, 10, 20... 500).

            زر "خصم نقاط" يفتح Modal لإدخال القيمة والسبب.

        Display: اسم الطالب، إجمالي النقاط الحالي، وعدد الصفحات المنجزة اليوم.

ب. ملف الطالب (Student Resource - View Page):

    عرض البيانات الشخصية.

    Relation Managers: جداول تحت الملف الشخصي تعرض (تاريخ التسميع، سجل النقاط، الملاحظات والشكاوى).

    الشكاوى: زر لإضافة ملاحظة مع تقييم من 10.

ج. صفحة الإحصائيات (Stats & Reports):

    استخدام Filament Widgets لعرض:

        إجمالي الصفحات المسمعة (أسبوعياً/شهرياً).

        ترتيب الطلاب حسب النقاط (Leaderboard).

        معدلات الانضباط (الحضور).
