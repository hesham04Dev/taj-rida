[x] the app should make me fast not slow 



---- 
x test the set other absence if it do it for only my students
x make it full arabic
x improve the design of the tracker 
x in give points make a btn for rem points -ve
x improve the design 
x make it production ready



[ ] in sura traker i want multi select so if a student memorize multi suras i can enter at once (note the ayas is all suras ayas no from to in this step)

[ ] fix mark other absence stop working after multi classes

[x] i want to show the percent of the sura if the student does not memorize it all

[x] remove rivison table and compine it with the memorization table
[x] add repetition for the rivision and memorization (no multi time for same sura) in same table
[x] show a lookup for the rivison on the memorization table dgree and rivision times
[x] now the table have new shape memorization repetion and degree and rivison repetion and degree (we only want to keep the last degree and last rivison degree)

[x] add tests for the suras in same memorization table i want to add (
    test_grade, test_counts, last_test_name, update_date (i want to see the date of last update to the memorization data), is_need_rememorisation
)
[x] add multi select for the suras  (so i can add multi memorization at once note from , to is always from the first aya to the last one foreach sura please consider that you should get the ayas_count from the suras table and put it in to feild) 

[x] get points for the pages that memorized , tested , revisioned
[x] add feild for the test points and make the giving point optional in test ()
[ ] make the page log for only see the log for the memorization table 
[x] note the points gived should be only for the pages that is not memorized yet eg: if student memorize from 1 -> 10 in 07-04-2026 and in 08-04-2026 he memorized from 1->20 he only get points from 10 -> 20 (the last number (10) is stored in the memorization memorized_pages)

[ ]return the relation of the page log to show the page log
[ ] preferd add a real links between the memorization so if edited the amount of points should re edited or idk


[x] add budget to the system
[x] add the points card to the system

[ ] add gifts calc to the system (need more working)
[ ] add whatsapp api or something for message automation
[ ] add knowledge base to the system and fill it 
[ ] what we should do when the dawra ends???!!!
[ ] update the logic and add classes so the student is linked to aclass not to ateacher and teacher can have access to multible classes very important 
[ ] very important add (sql insertion in the db for admin with a strong in file password so no one can insert the sql rather than me and it does not have a view in the filment just in the url )
{
2 ways
-first way just delete the points log only
-at the end of the dawara generate a log for what happens in this dawara for each student and its grade and just delete the points transaction and keep the memorizations
- createa a dawaras table and link every thing to it and for new dawara we create a new dawara 
}
[ ] add custom feilds to each table
[ ] add page for custom (custom title to it) (with custom links from the table that contains a md or somthing) 

[ ] the page logs need fix to match the new way and in the add log action in the student tracker should updated so the done pages background color is gray

[ ] in the new input way i think in adding a percentage for the last selected item (so we give him points according to that and store this in the db eg: he memorize 60% of sura Amma so when re memorize it just add points for the 40% of the sura)


هلق بخصوص الصفحات الي سمعها الشخص نحنا منقدر نعمل الزر نغير لونه مثلا رمادي اكثر من غيره بينعرف انه سمعها 
بس بعدني بخصوص الاقسام ما بشوف تتسجل الا كاملة مابعرف بدها مشاورة او شي

كان في شي معمول بس ما بعرف ليه اختفى الي هو يخصوص الي مسمع بينكتب حده الدرجة هي كنت عم اشتغل عليها ولسى ما خلصت 

هلق في مشكلة الي هي الي بحاجة للتسميع اتغير الاسلوب صار لازم يعرض الاب و الابن 
ولازم يقدر يدخلهم لحتى ينحفظ لايا ابن مو كامل الجزء او سورة 
يعني ص 5 (جزء 1)
او اذا المستخدم ضايف سور فبيطلعله ص5 (سورة البقرة)
ضايف اقسام 
القسم الثاني (سورة عم)


غير خط النظام ل ibm sans ك تجربة 

            done
            // TODO should take me to dawara section to set dawara not to stop the full app
            // and should stop all other active dawaras when add new active dawara to link data to it
            // TODO we should hide the debitor resource from other just the admin can view 
            // TODO add a box for the gifts balance to calcluate the exchange rate from the points to the money
            // add box to the expected rate for the points to money exchange rate and the admin can set it and the system will use it to calculate the expected money for the points
            // TODO the memorization should still be visible in other dawaras but without the points 
            // TODO add serd option in the memorization (or add away to add multible types of the memorization with its points)






done todo hide the dawara from non admin
           done // very important i want a way to insert sql in admin but just me with a password or  done somthing to prevent other from insert un wanted sql

           done // very important i want add classes to the dawara so student is linked to a class and the done teacher can teach multible classes and can select in witch class he is (in setting we set done the teacher can teach any class or some classes)


- [ ] we need a location to set the masjed name and logo  in the settings page
- [ ] re format the TODO page to realy know what done and what not





- [ ] add classes and teacher in the seeder 
- [ ] fix the bug in the leaderbord dialog we have the pages count is wrong (it count the pages without * with the point mulitplier as we want)


- [ ] AI NEW SHAPE (NEED REVISION)
- [x] ازالة عبارة قرض حسن من التطبيق
- [ ] شوف شو مشكلة الاجزاء المحفوظة ما عم تعرض شي ابدا


- [ ] see if this also in the production if yes it should be fixed (how to reproduce this error just use the system each time its show this error) or when try to edit student not very sure what trigger it 
Error
app/Filament/Resources/Students/RelationManagers/MemorizationsRelationManager.php:146

Call to a member function isInCurrentDawara() on null


- [ ] create a way to migrate from the production version to this version 3.3.0