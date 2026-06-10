function reindexQuestions(list) {
    list.querySelectorAll('[data-question]').forEach((questionEl, index) => {
        questionEl.querySelectorAll('[name]').forEach((field) => {
            field.name = field.name.replace(/questions\[[^\]]+\]/, `questions[${index}]`);
        });

        const label = questionEl.querySelector('[data-question-label]');
        if (label) {
            label.textContent = `Question ${index + 1}`;
        }

        const removeBtn = questionEl.querySelector('[data-remove-question]');
        if (removeBtn) {
            removeBtn.classList.toggle('hidden', list.querySelectorAll('[data-question]').length <= 1);
        }
    });
}

function syncQuestionType(questionEl) {
    const typeSelect = questionEl.querySelector('[data-question-type]');
    const mcSection = questionEl.querySelector('[data-mc-section]');

    if (!typeSelect || !mcSection) {
        return;
    }

    const isMc = typeSelect.value === 'multiple_choice';
    mcSection.classList.toggle('hidden', !isMc);

    mcSection.querySelectorAll('[name]').forEach((field) => {
        field.disabled = !isMc;
    });
}

function bindChoiceControls(questionEl) {
    const choicesList = questionEl.querySelector('[data-choices-list]');
    const addChoiceBtn = questionEl.querySelector('[data-add-choice]');

    addChoiceBtn?.addEventListener('click', () => {
        if (!choicesList) {
            return;
        }

        const choiceCount = choicesList.querySelectorAll('[data-choice]').length;
        const match = questionEl.querySelector('[name*="[question_text]"]')?.name.match(/questions\[(\d+)\]/);
        const qi = match ? match[1] : '0';

        const row = document.createElement('div');
        row.dataset.choice = '';
        row.className = 'flex items-center gap-2';
        row.innerHTML = `
            <input type="text" name="questions[${qi}][choices][${choiceCount}]" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Choice">
            <label class="flex items-center gap-1 text-xs whitespace-nowrap">
                <input type="radio" name="questions[${qi}][correct_option_index]" value="${choiceCount}" data-correct-option>
                Correct
            </label>
        `;
        choicesList.appendChild(row);
    });
}

function bindQuestion(questionEl, list) {
    syncQuestionType(questionEl);

    questionEl.querySelector('[data-question-type]')?.addEventListener('change', () => {
        syncQuestionType(questionEl);
    });

    questionEl.querySelector('[data-remove-question]')?.addEventListener('click', () => {
        if (list.querySelectorAll('[data-question]').length <= 1) {
            return;
        }

        questionEl.remove();
        reindexQuestions(list);
    });

    bindChoiceControls(questionEl);
}

function initQuizForm(root) {
    const list = root.querySelector('[data-questions-list]');
    const template = root.querySelector('template[data-question-template]');

    if (!list || !template) {
        return;
    }

    list.querySelectorAll('[data-question]').forEach((questionEl) => {
        bindQuestion(questionEl, list);
    });

    reindexQuestions(list);

    root.querySelector('[data-add-question]')?.addEventListener('click', () => {
        const count = list.querySelectorAll('[data-question]').length;

        if (count >= 100) {
            return;
        }

        const html = template.innerHTML.replaceAll('__INDEX__', String(count));
        list.insertAdjacentHTML('beforeend', html);

        const questionEl = list.lastElementChild;
        bindQuestion(questionEl, list);
        reindexQuestions(list);
    });

    root.closest('form')?.addEventListener('submit', (event) => {
        const invalidMc = [...list.querySelectorAll('[data-question]')].find((questionEl) => {
            const type = questionEl.querySelector('[data-question-type]')?.value;

            if (type !== 'multiple_choice') {
                return false;
            }

            const choices = [...questionEl.querySelectorAll('[data-choice] input[type="text"]')]
                .map((input) => input.value.trim())
                .filter(Boolean);

            if (choices.length < 2) {
                return true;
            }

            return !questionEl.querySelector('[data-correct-option]:checked');
        });

        if (invalidMc) {
            event.preventDefault();
            window.alert('Each multiple choice question needs at least 2 options and one marked as correct.');
        }
    });
}

function initQuizForms() {
    document.querySelectorAll('[data-quiz-form]').forEach(initQuizForm);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuizForms);
} else {
    initQuizForms();
}
