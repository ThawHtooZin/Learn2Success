document.addEventListener('alpine:init', () => {
    Alpine.data('teacherGradingForm', (config) => ({
        maxPerQuestion: config.maxPerQuestion,
        quizTotal: config.quizTotal,
        marks: config.marks,

        markValue(answerId) {
            const value = parseFloat(this.marks[answerId]);
            return Number.isFinite(value) ? value : 0;
        },

        get runningTotal() {
            return Object.keys(this.marks).reduce(
                (sum, id) => sum + this.markValue(id),
                0,
            );
        },

        remainingTotal() {
            return Math.max(0, this.quizTotal - this.runningTotal);
        },

        isOverTotal() {
            return this.runningTotal > this.quizTotal + 0.001;
        },

        questionMarkError(answerId) {
            const value = this.markValue(answerId);

            if (value > this.maxPerQuestion + 0.001) {
                return `Max ${this.formatMark(this.maxPerQuestion)} per question.`;
            }

            return '';
        },

        hasQuestionErrors() {
            return Object.keys(this.marks).some((id) => this.questionMarkError(id) !== '');
        },

        canSubmit() {
            return ! this.isOverTotal() && ! this.hasQuestionErrors();
        },

        formatMark(value) {
            return Number(value).toFixed(2).replace(/\.00$/, '');
        },

        onMarkInput(answerId, event) {
            this.marks[answerId] = event.target.value;
        },
    }));
});
