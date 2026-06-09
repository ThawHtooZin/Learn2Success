@props(['slides' => config('carousel.slides'), 'class' => ''])

<div
    x-data="imageCarousel({ slides: @js($slides) })"
    class="relative overflow-hidden rounded-3xl shadow-lg ring-2 ring-[#E3F2FD] {{ $class }}"
    @touchstart="touchStart = $event.changedTouches[0].screenX"
    @touchend="
        const diff = touchStart - $event.changedTouches[0].screenX;
        if (diff > 50) next();
        else if (diff < -50) prev();
    "
    x-init="touchStart = 0"
>
    <template x-for="(slide, index) in slides" :key="index">
        <div
            x-show="current === index"
            x-transition:enter="transition ease-out duration-400"
            x-transition:enter-start="opacity-0 translate-x-6"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="relative aspect-[2/1] w-full bg-gradient-to-br"
            :class="slide.color"
        >
            <img
                :src="slide.image.startsWith('http') ? slide.image : ('/' + slide.image)"
                :alt="slide.title"
                class="absolute inset-0 h-full w-full p-2"
                :class="(slide.fit || 'cover') === 'contain' ? 'object-contain object-center' : 'object-cover object-center p-0'"
            >
            <div
                x-show="slide.showCaption !== false"
                class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 via-black/40 to-transparent px-5 pb-6 pt-14 text-white"
            >
                <p class="text-2xl font-bold leading-tight drop-shadow-sm" x-text="slide.title"></p>
                <p class="mt-1 text-base font-semibold text-white/95" x-text="slide.caption"></p>
            </div>
        </div>
    </template>

    <template x-if="slides.length > 1">
        <div class="absolute bottom-3 left-0 right-0 z-10 flex justify-center gap-2">
            <template x-for="(slide, index) in slides" :key="'dot-'+index">
                <button
                    type="button"
                    @click="goTo(index)"
                    class="h-3 rounded-full transition-all"
                    :class="current === index ? 'w-9 bg-[#ffc107]' : 'w-3 bg-white/60'"
                    :aria-label="'Slide ' + (index + 1)"
                ></button>
            </template>
        </div>
    </template>
</div>
