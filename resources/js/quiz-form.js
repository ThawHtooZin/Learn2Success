document.addEventListener('alpine:init', () => {
    Alpine.data('quizForm', (config) => ({
        questions: config.questions,

        addQuestion() {
            if (this.questions.length >= 100) return;
            this.questions.push({
                question_text: '',
                question_type: 'recording',
                choices: ['', ''],
                correct_option_indexes: [],
            });
        },

        removeQuestion(index) {
            if (this.questions.length <= 1) return;
            this.questions.splice(index, 1);
        },

        addChoice(qi) {
            this.questions[qi].choices.push('');
        },

        toggleCorrect(qi, ci) {
            const q = this.questions[qi];
            const idx = q.correct_option_indexes.indexOf(ci);
            q.correct_option_indexes = idx === -1 ? [ci] : [];
        },
    }));
});
