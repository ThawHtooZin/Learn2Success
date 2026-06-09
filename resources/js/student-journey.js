document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.journey-week-node--unlocked').forEach((node, index) => {
        node.style.animationDelay = `${index * 0.08}s`;
    });

    document.querySelectorAll('.journey-quiz-step').forEach((step, index) => {
        step.style.animationDelay = `${index * 0.06}s`;
    });
});
