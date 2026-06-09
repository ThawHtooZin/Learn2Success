document.addEventListener('alpine:init', () => {
    Alpine.data('weekQuizManager', (config) => ({
        quizzes: config.quizzes,
        availableQuizzes: config.availableQuizzes,
        selectedQuizId: '',
        dragIndex: null,
        saving: false,
        feedback: '',
        feedbackType: 'success',
        initialized: false,

        init() {
            this.initialized = true;
        },

        csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },

        showFeedback(message, type = 'success') {
            this.feedback = message;
            this.feedbackType = type;
            setTimeout(() => {
                this.feedback = '';
            }, 3000);
        },

        formatTime(seconds) {
            if (!seconds) return '—';
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        },

        async addQuiz() {
            if (!this.selectedQuizId) return;

            const response = await fetch(config.assignUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({ quiz_id: Number(this.selectedQuizId) }),
            });

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                this.showFeedback(data.message ?? 'Could not add quiz.', 'error');
                return;
            }

            const data = await response.json();
            this.quizzes.push(data.quiz);
            this.availableQuizzes = this.availableQuizzes.filter(
                (q) => q.id !== data.quiz.id,
            );
            this.selectedQuizId = '';
            this.showFeedback('Quiz added to week.');
        },

        async removeQuiz(quizId) {
            if (!confirm('Remove this quiz from the week?')) return;

            const response = await fetch(config.removeUrlTemplate.replace('__QUIZ__', quizId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrf(),
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                this.showFeedback('Could not remove quiz.', 'error');
                return;
            }

            const removed = this.quizzes.find((q) => q.id === quizId);
            this.quizzes = this.quizzes.filter((q) => q.id !== quizId);

            if (removed) {
                this.availableQuizzes.push({ id: removed.id, title: removed.title });
                this.availableQuizzes.sort((a, b) => a.title.localeCompare(b.title));
            }

            this.showFeedback('Quiz removed from week.');
        },

        dragStart(index) {
            this.dragIndex = index;
        },

        dragOver(event, index) {
            event.preventDefault();
            if (this.dragIndex === null || this.dragIndex === index) return;

            const moved = this.quizzes.splice(this.dragIndex, 1)[0];
            this.quizzes.splice(index, 0, moved);
            this.dragIndex = index;
        },

        async dragEnd() {
            this.dragIndex = null;
            await this.saveOrder();
        },

        async moveUp(index) {
            if (index <= 0) return;
            const item = this.quizzes.splice(index, 1)[0];
            this.quizzes.splice(index - 1, 0, item);
            await this.saveOrder();
        },

        async moveDown(index) {
            if (index >= this.quizzes.length - 1) return;
            const item = this.quizzes.splice(index, 1)[0];
            this.quizzes.splice(index + 1, 0, item);
            await this.saveOrder();
        },

        async saveOrder() {
            if (this.saving) return;
            this.saving = true;

            const response = await fetch(config.reorderUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    quiz_ids: this.quizzes.map((q) => q.id),
                }),
            });

            this.saving = false;

            if (!response.ok) {
                this.showFeedback('Could not save order.', 'error');
                return;
            }

            this.showFeedback('Order saved.');
        },
    }));
});
