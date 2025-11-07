<x-sidebar.overlay />

<aside
    class="fixed inset-y-0 z-40 flex w-72 flex-col gap-6 rounded-r-[32px] border border-[#d7f0d7] bg-white/95 px-4 py-6 text-slate-700 shadow-[0_28px_60px_-25px_rgba(0,102,0,0.55)] backdrop-blur-xl"
    :class="{
        'translate-x-0': isSidebarOpen || isSidebarHovered,
        '-translate-x-full': !isSidebarOpen && !isSidebarHovered,
    }"
    style="transition-property: width, transform; transition-duration: 150ms;"
    x-on:mouseenter="handleSidebarHover(true)"
    x-on:mouseleave="handleSidebarHover(false)"
>
    <x-sidebar.header />

    <x-sidebar.content />

    <x-sidebar.footer />
</aside>
