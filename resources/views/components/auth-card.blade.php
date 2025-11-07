<main class="flex flex-col items-center flex-1 px-4 pt-6 sm:justify-center">
    <div>
        <a href="/">
            <x-application-logo class="w-20 h-20" />
        </a>
    </div>

    <div class="my-6 w-full max-w-md rounded-[32px] border border-[#d7f0d7] bg-white/95 px-6 py-6 shadow-xl shadow-[#009900]/10 backdrop-blur-xl">
        {{ $slot }}
    </div>
</main>