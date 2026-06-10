document.addEventListener('alpine:init', () => {
    Alpine.data('quizForm', (config) => ({
        questions: [],

        init() {
            this.questions = (config.questions ?? []).map((q) => this.normalizeQuestion(q));
        },

        normalizeQuestion(question) {
            const q = { ...question };

            if (!Array.isArray(q.choices) || q.choices.length < 2) {
                q.choices = ['', ''];
            }

            if (!Array.isArray(q.correct_option_indexes)) {
                q.correct_option_indexes = [];
            }

            return q;
        },

        addQuestion() {
            if (this.questions.length >= 100) return;
            this.questions.push(this.normalizeQuestion({
                question_text: '',
                question_type: 'recording',
            }));
        },

        removeQuestion(index) {
            if (this.questions.length <= 1) return;
            this.questions.splice(index, 1);
        },

        addChoice(qi) {
            this.questions[qi].choices.push('');
        },

        onTypeChange(qi) {
            this.questions[qi] = this.normalizeQuestion(this.questions[qi]);
        },

        toggleCorrect(qi, ci) {
            const q = this.questions[qi];
            const indexes = q.correct_option_indexes ?? [];
            const idx = indexes.indexOf(ci);
            q.correct_option_indexes = idx === -1 ? [ci] : [];
        },

        isCorrect(qi, ci) {
            return (this.questions[qi].correct_option_indexes ?? []).includes(ci);
        },
    }));
});
