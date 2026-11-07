🚨 1. Critical Bugs & v3.3.0 Release Blockers

    [ ] Fix isInCurrentDawara() Null Error (Production Check):

        Location: app/Filament/Resources/Students/RelationManagers/MemorizationsRelationManager.php:146

        Error: Call to a member function isInCurrentDawara() on null

        Action: Check why the related model or Dawra relation returns null when viewing or editing a student, add null-safe handling (?->isInCurrentDawara()), and verify if the bug exists in production.

    [ ] Fix Empty "Memorized Juz" (الأجزاء المحفوظة) View: Investigate why the Memorized Juz section currently renders no data at all.

    [ ] Fix Leaderboard Page Count Bug: Update the leaderboard dialog calculation—it currently counts raw pages without applying the points multiplier as intended.

    [ ] v3.3.0 Production Migration Path: Build a migration script/workflow to safely upgrade the live production database to schema version 3.3.0.

    [ ] Update Database Seeders: Add Classes and Teachers (with their new multi-class relationships) to the database seeder.

    [ ] remove bulk log or make it for the test or serd improve it 

🛠️ 2. Memorization, Tracker & Page Logs (Core Logic)

    [ ] Fix "Needs Re-memorization" (is_need_rememorisation) Parent-Child Display & Input:

        Update the tracker to record and display the specific Child (Parent) item rather than flagging the entire Juz or Surah.

        Examples: Page 5 (Juz 1) / ص 5 (جزء 1), Page 5 (Surah Al-Baqarah) / ص 5 (سورة البقرة), or Section 2 (Surah Amma) / القسم الثاني (سورة عم).

    [x] Restore Grade Display Next to Memorized Items: Finish and restore the missing UI indicator that shows the student's grade next to already-memorized items in the tracker.

    [ ] Partial Percentage Input on Multi-Select: Allow setting a completion percentage for the last selected item in a multi-select batch (e.g., 60% of Surah Amma), awarding points for that 60% now and only awarding the remaining 40% when completed later.
    also one for the first (what i am thinking in [ui show 4 feilds (0->25% , 25->50%, 50->75%, 75->100%)with a button for is not full page option ] can be handled in 1 json or 4 feild or any other thing in the db)

    [ ] Decide on Partial Sections (الأقسام) Recording: Review whether sections should only be logged when 100% complete or if partial section logging should be allowed. ???

    [x] Page Log Relation & Filtering: 

        Restore the pageLog relationship so logs display properly.

        Scope the Page Log view to only display logs belonging to the memorization table.

    [x] Update Page Log Tracker UI: Refactor the "Add Log" action in the student tracker to match the new workflow and style completed page buttons in a darker gray (رمادي أغمق) so memorized pages stand out clearly.

    [ ] Dynamic Point Recalculation on Memorization Edit: Add a direct foreign-key relationship between memorization records and point transactions so editing or deleting a memorization record automatically adjusts the awarded points. (idk if done or not )

✨ 3. Settings, Features & UI Enhancements (Backlog)

    [ ] Settings — Masjid Identity: Add fields in the Settings page to configure the Masjid Name and Masjid Logo.

    [ ] Typography Test: Change the system UI font to IBM Plex Sans Arabic as a trial.

    [ ] Refine Gifts Calculator: Expand and polish the gifts calculation logic beyond the basic balance/exchange-rate boxes.

    [ ] End-of-Dawra Archival & Summary Report: Finalize the end-of-Dawra transition behavior:

        Generate an archive summary log per student (grades and activity during the Dawra).

        Clear/archive point transactions for the new Dawra while keeping historical memorization records intact.

        also i want to test the current stuation

    [ ] Dynamic Custom Fields: Add support for custom fields across system tables.

    [ ] Custom Markdown Pages: Create a custom page builder with a configurable title, Markdown content support, and custom navigation links from tables.

    [ ] Knowledge Base: Build a Knowledge Base module inside the system and populate it with user guides.

    [ ] WhatsApp Automation: Integrate a WhatsApp API for automated student/parent notifications.

    [ ] telegram and sms also 

    [ ] AI Module Revision: Review and revise the AI NEW SHAPE implementation.

✅ 4. Completed Tasks ([x])
Dawra, Classes & Admin Security

    [x] Multi-Class Teacher Architecture: Link students to a Class instead of directly to a Teacher; allow teachers to access multiple classes (configurable in settings: all classes vs. assigned classes).

    [x] Dawra Lifecycle & Visibility:

        Redirect to the Dawra setup section when no active Dawra exists instead of blocking the entire app.

        Automatically deactivate previous active Dawras when creating/activating a new Dawra.

        Keep historical memorization records visible across other Dawras without carrying over their points.

        Hide the Dawra resource from non-admin users.

    [x] Protected Admin SQL Runner: Add a URL-only, password-protected SQL execution page for the Admin (hidden from the Filament sidebar).

    [x] Restrict Debtor Resource: Hide the Debtor resource from regular users so only Admins can view it.

Memorization, Revision & Testing Engine

    [x] Unified Memorization & Revision Table: Remove the separate Revision table and merge it into the Memorization table with repetition counters (memorization_repetition, revision_repetition), keeping only the latest memorization degree and latest revision degree plus lookup display.

    [x] Integrated Surah Testing: Add test tracking fields directly to the memorization table (test_grade, test_counts, last_test_name, update_date, is_need_rememorisation, and optional test_points).

    [x] Multi-Select Surah Entry: Allow selecting multiple full Surahs at once, automatically pulling ayas_count from the suras table to populate the from (1) and to (ayas_count) fields.

    [x] Multiple Memorization Types (سرد): Add the "Sard" option / multiple memorization types with customizable point values.

    [x] Surah Completion Percentage: Show completion percentage when a student has only partially memorized a Surah.

Points, Budget & General UX

    [x] Incremental Page Points Logic: Award points strictly for newly memorized pages (e.g., memorizing pages 1 to 10 on 07-04-2026 and pages 1 to 20 on 08-04-2026 awards points only for pages 10 to 20 using memorized_pages).

    [x] Activity Points: Calculate and award points across memorized, tested, and revised pages.

    [x] Negative Points Action: Add a button to deduct points (-ve) in the Give Points dialog.

    [x] Budget, Points Card & Gifts Exchange Rate: Add system budget tracking, student points cards, a gifts balance widget, and an admin-configurable expected points-to-money exchange rate.

    [x] UI, Localization & Production Readiness: Full Arabic localization, removal of the phrase "قرض حسن", tracker and overall UI redesign, absence scoping exclusively to the teacher's own students, and speed optimizations.