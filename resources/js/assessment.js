/**
 * Assessment Form JavaScript
 * Handles multi-step form navigation, validation, and submission
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('assessmentForm');
    if (!form) return;

    const steps = document.querySelectorAll('.question-step');
    const totalSteps = steps.length;
    let currentStep = 0;
    const responses = {};

    const progressBar = document.getElementById('progressBar');
    const progressBadge = document.getElementById('progressBadge');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const responsesInput = document.getElementById('responsesInput');

    // ===== Step Navigation =====
    function showStep(index) {
        steps.forEach((step, i) => {
            step.style.display = i === index ? 'block' : 'none';
            step.classList.toggle('fade-in', i === index);
        });

        const progress = ((index + 1) / totalSteps) * 100;
        if (progressBar) progressBar.style.width = progress + '%';
        if (progressBadge) progressBadge.textContent = `Step ${index + 1} of ${totalSteps}`;

        if (prevBtn) prevBtn.disabled = index === 0;
        if (nextBtn) nextBtn.style.display = index < totalSteps - 1 ? 'inline-flex' : 'none';
        if (submitBtn) submitBtn.style.display = index === totalSteps - 1 ? 'inline-flex' : 'none';

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ===== Save Current Step =====
    function saveCurrentStep() {
        const step = steps[currentStep];
        if (!step) return;

        const key = step.dataset.key;
        if (!key) return;

        const selectedRadio = step.querySelector('input[type="radio"]:checked');
        const textarea = step.querySelector('textarea');
        const textInput = step.querySelector('input[type="text"]');

        if (selectedRadio) {
            responses[key] = {
                key: key,
                text: selectedRadio.dataset.text || selectedRadio.value,
                score: parseInt(selectedRadio.value, 10),
            };
        } else if (textarea && textarea.value.trim()) {
            responses[key] = {
                key: key,
                text: textarea.value.trim(),
                score: null,
            };
        } else if (textInput && textInput.value.trim()) {
            responses[key] = {
                key: key,
                text: textInput.value.trim(),
                score: null,
            };
        }
    }

    // ===== Validate Current Step =====
    function validateCurrentStep() {
        const step = steps[currentStep];
        if (!step) return true;

        const key = step.dataset.key;
        if (!key) return true; // Video step is optional

        const selectedRadio = step.querySelector('input[type="radio"]:checked');
        const textarea = step.querySelector('textarea');

        // Textarea questions are optional, radio questions require selection
        const hasRadios = step.querySelector('input[type="radio"]');
        if (hasRadios && !selectedRadio) {
            showToast('Please select an option to continue.', 'warning');
            return false;
        }

        return true;
    }

    // ===== Event Handlers =====

    // Option click
    document.querySelectorAll('.option-item').forEach((item) => {
        item.addEventListener('click', function () {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });

    // Radio change
    document.querySelectorAll('input[type="radio"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            const parent = this.closest('.question-step');
            if (!parent) return;

            parent.querySelectorAll('.option-item').forEach((item) => {
                item.classList.remove('border-primary', 'bg-light');
            });

            const optionItem = this.closest('.option-item');
            if (optionItem) {
                optionItem.classList.add('border-primary', 'bg-light');
            }
        });
    });

    // Next button
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (!validateCurrentStep()) return;
            saveCurrentStep();

            if (currentStep < totalSteps - 1) {
                currentStep++;
                showStep(currentStep);
            }
        });
    }

    // Previous button
    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            saveCurrentStep();
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }

    // Form submit
    form.addEventListener('submit', function (e) {
        saveCurrentStep();

        const responsesArray = Object.values(responses);
        if (responsesInput) {
            responsesInput.value = JSON.stringify(responsesArray);
        }

        if (responsesArray.length === 0) {
            e.preventDefault();
            showToast('Please answer at least one question before submitting.', 'danger');
            return;
        }

        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight' && nextBtn && !nextBtn.disabled && nextBtn.style.display !== 'none') {
            nextBtn.click();
        } else if (e.key === 'ArrowLeft' && prevBtn && !prevBtn.disabled) {
            prevBtn.click();
        }
    });

    // Initialize
    showStep(0);
});