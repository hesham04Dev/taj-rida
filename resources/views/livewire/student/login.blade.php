<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25 mb-4">
                <flux:icon.academic-cap class="size-8 text-white" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">تاج الرضا</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">بوابة الطالب</p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-8 shadow-2xl">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-6 text-center">تسجيل الدخول</h2>

            <form wire:submit="submit" class="space-y-5">
                <flux:field>
                    <flux:label>رمز الدخول</flux:label>
                    <flux:input
                        id="access_code"
                        wire:model="access_code"
                        type="text"
                        placeholder="أدخل رمز الدخول الخاص بك"
                        autocomplete="off"
                        class="text-center tracking-widest text-lg"
                        autofocus
                    />
                    <flux:error name="access_code" />
                </flux:field>

                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 mt-2"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>دخول</span>
                    <span wire:loading>جاري التحقق...</span>
                </flux:button>
            </form>
        </div>

        <p class="text-center text-xs text-zinc-500 dark:text-zinc-600 mt-6">
            إذا نسيت رمز الدخول، تواصل مع معلمك
        </p>
    </div>
</div>
