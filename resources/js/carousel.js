document.addEventListener('alpine:init', () => {
    Alpine.data('imageCarousel', (config) => ({
        slides: config.slides ?? [],
        current: 0,
        timer: null,
        touchStart: null,
        pointerStart: null,

        init() {
            this.resetTimer();
        },

        destroy() {
            if (this.timer) {
                clearInterval(this.timer);
            }
        },

        resetTimer() {
            if (this.timer) {
                clearInterval(this.timer);
            }

            if (this.slides.length <= 1) {
                return;
            }

            this.timer = setInterval(() => this.next(false), 4500);
        },

        goTo(index) {
            this.current = index;
            this.resetTimer();
        },

        next(resetTimer = true) {
            this.current = (this.current + 1) % this.slides.length;

            if (resetTimer) {
                this.resetTimer();
            }
        },

        prev(resetTimer = true) {
            this.current = (this.current - 1 + this.slides.length) % this.slides.length;

            if (resetTimer) {
                this.resetTimer();
            }
        },

        onTouchStart(event) {
            this.touchStart = event.changedTouches[0].screenX;
        },

        onTouchEnd(event) {
            if (this.touchStart === null) {
                return;
            }

            const diff = this.touchStart - event.changedTouches[0].screenX;
            this.touchStart = null;
            this.handleSwipe(diff);
        },

        onPointerDown(event) {
            if (event.pointerType === 'mouse' && event.button !== 0) {
                return;
            }

            this.pointerStart = event.clientX;
        },

        onPointerUp(event) {
            if (this.pointerStart === null) {
                return;
            }

            const diff = this.pointerStart - event.clientX;
            this.pointerStart = null;
            this.handleSwipe(diff);
        },

        handleSwipe(diff) {
            if (diff > 50) {
                this.next();
            } else if (diff < -50) {
                this.prev();
            }
        },
    }));
});
