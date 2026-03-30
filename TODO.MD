make it full arabic
fix the tables
fix the forms
make it productions no artisan serve
improve the design 
add en lang like silah
sort student by points or by attendance

i want custom page that shows all my student 
with easy bt



----
the admin have all student as his student
in the student resource we want custom view or card view with fast adding values for the rivision and attendnce and memorisation


[-] very important addition each studnet have suras and each sura have a color depends on (gray: not memorized, lime_green:rivisened excellent , dark_green:rivisoned good, light_blue:memorized excelent , blue memorized good, yellow memorized not good and each sura is clickable to see the log of memorization and rivision )

[ ] for the above todo (improve the design make it looks profissional use tailwind ) and from , to should be in one line and auto filled with the aya 1 and the last aya of the sura
 [ ] another related point for fast i dont want a select for the type i want a button (radio) also for the grade the same thing that will improve the speed

 [ ] add tracker button in the student page


 [ ] in the student page i want grid view 1 per line to have a good space (mobile first design )
 eg:
 ```php
 return $table
        // 1. Set the grid: 1 card on mobile, 2 on tablet, 3 on desktop
        ->contentGrid([
            'md' => 2,
            'xl' => 2,
            'sm' => 1
        ])
        ->columns([
            Stack::make([
                // The Top Section: Title
                TextColumn::make('title')
                    ->label(__('title'))
                    ->weight("bold")
                    ->size("large")
                    ->searchable()
                    ->sortable(),

                // The Middle Section: Subtext (User Name)
                TextColumn::make('user.name')
                    ->label(__('Suggested By'))
                    ->color('gray')
                    ->size("small")
                    ->icon('heroicon-m-user')
                    ->sortable(),

                // The Bottom Section: A Split row for Status and Likes
                Split::make([
                    // Status Badge
                    TextColumn::make('status')
                        ->label(__('status'))
                        ->formatStateUsing(fn (string $state): string => __($state))
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'planned' => 'primary',
                            'done' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        })->grow(false),
                        
                    TextColumn::make('type')
                        ->formatStateUsing(fn (string $state): string => __($state))
                        ->badge()
                        ->sortable()
                        ->color('gray')
                        ->grow(false),

                    // Likes Count with Icon
                    TextColumn::make('likes_count')
                        ->label(__('likes'))
                        ->icon('heroicon-m-hand-thumb-up')
                        ->badge()
                        ->color('success')
                        ->sortable(),
                    TextColumn::make("created_at")
                    ->sortable()
                    ->date()
                    ->alignEnd()
                ]),
            ])->space(3), // Adds nice breathing room between rows
        ])
 ```


 [ ] i want total points foreach student to be shown in the grid with nice shape

[ ] the app should make me fast not slow 
[-] i think we should build a custom page for student or to improve the student table so i dont need to enter student for adding attendance , page log or adding point this should be available from out side (note the attendance should be for now not a date select) each transaction or adding is for now date and time


[ ] also in the table grid view add btn for the page log with the fast btn not select inside the dialog


[x] you dont consider this point in absence (the default is not absence since this did not remove points from the student ) i want a btn that get look for all my stduent and not attend set them absence 



---- 
x test the set other absence if it do it for only my students
x make it full arabic
x improve the design of the tracker 
x in give points make a btn for rem points -ve
x improve the design 
x make it production ready



[ ] in sura traker i want multi select so if a student memorize multi suras i can enter at once (note the ayas is all suras ayas no from to in this step)


[ ] i want to show the percent of the sura if the student does not memorize it all

[ ] remove rivison table and compine it with the memorization table
[ ] add repetition for the rivision and memorization (no multi time for same sura) in same table
[ ] show a lookup for the rivison on the memorization table dgree and rivision times
[ ] now the table have new shape memorization repetion and degree and rivison repetion and degree (we only want to keep the last degree and last rivison degree)

