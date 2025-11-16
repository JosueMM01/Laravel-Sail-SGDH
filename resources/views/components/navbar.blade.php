<nav
    aria-label="secondary"
    x-data="{ open: false }"
    class="sticky top-0 z-20 mx-4 mt-4 flex items-center justify-between rounded-3xl border border-[#d7f0d7] bg-white/90 px-4 py-4 text-slate-600 shadow-xl shadow-[#009900]/10 backdrop-blur-xl transition-transform duration-500 sm:px-6"
    :class="{
        '-translate-y-full': scrollingDown,
        'translate-y-0': scrollingUp,
    }">

    <div class="flex items-center gap-3">
        <x-button
            type="button"
            icon-only
            variant="secondary"
            sr-text="Alternar menú lateral"
            x-on:click="isSidebarOpen = !isSidebarOpen"
        >
            <x-heroicon-o-bars-3
                x-show="!isSidebarOpen"
                class="h-6 w-6"
                aria-hidden="true"
            />

            <x-heroicon-o-x-mark
                x-show="isSidebarOpen"
                class="h-6 w-6"
                aria-hidden="true"
            />
        </x-button>
    </div>

    <div class="flex items-center gap-3">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button
                    class="flex items-center gap-2 rounded-2xl border border-transparent bg-white/70 px-3 py-2 text-sm font-semibold text-[#006600] shadow-sm transition hover:border-[#009900]/30 hover:bg-[#f4fbf4] focus:outline-none focus:ring-2 focus:ring-[#006600]/60 focus:ring-offset-2 focus:ring-offset-white"
                >
                    <div class="hidden text-xs font-semibold uppercase tracking-[0.35em] text-[#009900]/80 sm:block">Usuario</div>
                    <div>{{ Auth::user()->name }}</div>

                    <div class="ml-1">
                        <svg
                            class="h-4 w-4 fill-current"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col gap-1">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Perfil') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-dropdown-link
                            :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            {{ __('Cerrar Sesión') }}
                        </x-dropdown-link>
                    </form>
                </div>
            </x-slot>
        </x-dropdown>
    </div>
</nav>
