document.addEventListener('alpine:init', () => {
    Alpine.data('imageCarousel', (config) => ({
        slides: config.slides ?? [],
        current: 0,
        timer: null,

        init() {
            if (this.slides.length <= 1) return;
            this.timer = setInterval(() => this.next(), 4500);
        },

        destroy() {
            if (this.timer) clearInterval(this.timer);
        },

        goTo(index) {
            this.current = index;
        },

        next() {
            this.current = (this.current + 1) % this.slides.length;
        },

        prev() {
            this.current = (this.current - 1 + this.slides.length) % this.slides.length;
        },
    }));
});
