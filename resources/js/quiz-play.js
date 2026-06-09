document.addEventListener('alpine:init', () => {
    Alpine.data('quizPlay', (config) => ({
        questions: config.questions,
        currentIndex: 0,
        completeUrl: config.completeUrl,
        startedAt: config.startedAt,
        timeLimitSeconds: config.timeLimitSeconds ?? null,
        remainingSeconds: config.timeLimitSeconds ?? null,
        quizTimer: null,
        timeExpired: false,
        recording: false,
        countdown: 0,
        recordSeconds: 0,
        mediaRecorder: null,
        chunks: [],
        previewUrl: null,
        error: '',
        recordTimer: null,
        countdownTimer: null,

        get currentQuestion() {
            return this.questions[this.currentIndex] ?? null;
        },

        listen(text) {
            if (!('speechSynthesis' in window)) {
                this.error = 'Text-to-speech is not supported in this browser.';
                return;
            }
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';
            speechSynthesis.speak(utterance);
        },

        async startRecording() {
            this.error = '';
            this.countdown = 3;

            this.countdownTimer = setInterval(() => {
                this.countdown -= 1;
                if (this.countdown <= 0) {
                    clearInterval(this.countdownTimer);
                    this.countdown = 0;
                    this.beginRecording();
                }
            }, 1000);
        },

        async beginRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.chunks = [];
                this.mediaRecorder = new MediaRecorder(stream);
                this.mediaRecorder.ondataavailable = (e) => this.chunks.push(e.data);
                this.mediaRecorder.onstop = () => {
                    stream.getTracks().forEach((t) => t.stop());
                    const blob = new Blob(this.chunks, { type: 'audio/webm' });
                    if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                    this.previewUrl = URL.createObjectURL(blob);
                    this.uploadAudio(blob);
                };
                this.mediaRecorder.start();
                this.recording = true;
                this.recordSeconds = 0;
                this.recordTimer = setInterval(() => {
                    this.recordSeconds += 1;
                    if (this.recordSeconds >= 30) this.stopRecording();
                }, 1000);
            } catch {
                this.error = 'Microphone access denied or unavailable.';
            }
        },

        stopRecording() {
            if (!this.recording || !this.mediaRecorder) return;
            clearInterval(this.recordTimer);
            this.recording = false;
            this.mediaRecorder.stop();
        },

        async uploadAudio(blob) {
            const q = this.currentQuestion;
            const form = new FormData();
            form.append('audio', blob, 'recording.webm');

            const res = await fetch(q.audio_upload_url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: form,
            });

            if (!res.ok) {
                this.error = 'Upload failed. Please try again.';
                return;
            }

            const data = await res.json();
            q.audio_url = data.audio_url;
        },

        async saveSelection(selected) {
            const q = this.currentQuestion;
            q.selected_options = selected;

            await fetch(q.selection_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ selected_options: selected }),
            });
        },

        prev() {
            if (this.currentIndex > 0) this.currentIndex -= 1;
        },

        async next() {
            if (this.timeExpired) {
                return;
            }

            const q = this.currentQuestion;

            if (q.question_type === 'recording' && !q.audio_url) {
                this.error = 'Please record an answer before continuing.';
                return;
            }

            if (q.question_type !== 'recording' && (!q.selected_options || q.selected_options.length === 0)) {
                this.error = 'Please select an answer before continuing.';
                return;
            }

            this.error = '';

            if (this.currentIndex < this.questions.length - 1) {
                this.currentIndex += 1;
                this.previewUrl = this.currentQuestion.audio_url;
                return;
            }

            const duration = Math.round((Date.now() - this.startedAt) / 1000);
            await this.completeQuiz(false);
        },

        formatTime(totalSeconds) {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return `${minutes}:${String(seconds).padStart(2, '0')}`;
        },

        startQuizTimer() {
            if (!this.timeLimitSeconds || this.quizTimer) {
                return;
            }

            this.remainingSeconds = this.timeLimitSeconds;
            this.quizTimer = setInterval(() => {
                if (this.remainingSeconds <= 0) {
                    this.handleTimeExpired();
                    return;
                }

                this.remainingSeconds -= 1;
            }, 1000);
        },

        handleTimeExpired() {
            if (this.timeExpired) {
                return;
            }

            this.timeExpired = true;
            clearInterval(this.quizTimer);
            this.quizTimer = null;
            this.error = 'Time is up! Submitting your quiz…';
            this.completeQuiz(true);
        },

        async completeQuiz(force = false) {
            const duration = Math.round((Date.now() - this.startedAt) / 1000);
            const res = await fetch(this.completeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ duration_seconds: duration, time_expired: force }),
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                this.error = err.message || 'Could not complete quiz. Answer all questions.';
                this.timeExpired = false;
                return;
            }

            const data = await res.json();
            window.location.href = data.redirect;
        },

        init() {
            if (this.currentQuestion?.audio_url) {
                this.previewUrl = this.currentQuestion.audio_url;
            }

            this.startQuizTimer();
        },

        destroy() {
            if (this.quizTimer) {
                clearInterval(this.quizTimer);
            }
        },
    }));
});
