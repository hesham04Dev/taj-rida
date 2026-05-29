<div class="min-h-screen flex items-center justify-center w-full">
    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-lg shadow-indigo-500/25 mb-4">
                <flux:icon.user-group class="size-8 text-white" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">تاج الرضا</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">بوابة أولياء الأمور</p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-8 shadow-2xl">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-6 text-center">تسجيل الدخول للمتابعة</h2>

            <form wire:submit="submit" class="space-y-5">
                <flux:field>
                    <flux:label>رقم الهاتف</flux:label>
                    <flux:input
                        id="phone"
                        wire:model="phone"
                        type="text"
                        placeholder="أدخل رقم الهاتف الخاص بك"
                        autocomplete="tel"
                        class="text-center text-lg"
                        autofocus
                    />
                    <flux:error name="phone" />
                </flux:field>

                <flux:field>
                    <flux:label>كلمة المرور</flux:label>
                    <flux:input
                        id="password"
                        wire:model="password"
                        type="password"
                        placeholder="أدخل كلمة المرور"
                        autocomplete="current-password"
                        class="text-center text-lg"
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 mt-2"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>دخول</span>
                    <span wire:loading>جاري التحقق...</span>
                </flux:button>
            </form>
        </div>

        <p class="text-center text-xs text-zinc-500 dark:text-zinc-600 mt-6">
            إذا لم يكن لديك حساب أو نسيت كلمة المرور، يرجى التواصل مع إدارة الحلقة
        </p>
    </div>
</div>
