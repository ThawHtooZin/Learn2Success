@props(['slides' => config('carousel.slides'), 'class' => ''])

<div
    x-data="imageCarousel({ slides: @js($slides) })"
    class="welcome-carousel relative w-full max-w-full overflow-hidden rounded-2xl shadow-lg ring-2 ring-[#E3F2FD] sm:rounded-3xl {{ $class }}"
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
            class="welcome-carousel__slide relative aspect-[4/3] w-full bg-gradient-to-br sm:aspect-[16/10] lg:aspect-[2/1]"
            :class="slide.color"
        >
            <img
                :src="slide.image.startsWith('http') ? slide.image : ('/' + slide.image)"
                :alt="slide.title"
                class="absolute inset-0 h-full w-full"
                :class="(slide.fit || 'cover') === 'contain' ? 'object-contain object-center p-3 sm:p-4' : 'object-cover object-center'"
                loading="lazy"
            >
            <div
                x-show="slide.showCaption !== false"
                class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 via-black/40 to-transparent px-4 pb-5 pt-10 text-white sm:px-5 sm:pb-6 sm:pt-14"
            >
                <p class="text-lg font-bold leading-tight drop-shadow-sm sm:text-2xl" x-text="slide.title"></p>
                <p class="mt-1 text-sm font-semibold text-white/95 sm:text-base" x-text="slide.caption"></p>
            </div>
        </div>
    </template>

    <template x-if="slides.length > 1">
        <div class="absolute bottom-2 left-0 right-0 z-10 flex justify-center gap-1.5 sm:bottom-3 sm:gap-2">
            <template x-for="(slide, index) in slides" :key="'dot-'+index">
                <button
                    type="button"
                    @click="goTo(index)"
                    class="h-2.5 rounded-full transition-all sm:h-3"
                    :class="current === index ? 'w-7 bg-[#ffc107] sm:w-9' : 'w-2.5 bg-white/60 sm:w-3'"
                    :aria-label="'Slide ' + (index + 1)"
                ></button>
            </template>
        </div>
    </template>
</div>
