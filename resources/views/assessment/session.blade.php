@extends('layouts.app')

@section('title', 'Live Assessment')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-9">

        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Session</span>
                        <span class="ms-2"><code>{{ substr($session->session_token, 0, 8) }}</code></span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <span class="text-muted small">Risk</span>
                            <span id="liveRiskPill" class="risk-badge risk-low ms-1">Low</span>
                        </div>
                        <div>
                            <span class="text-muted small">Progress</span>
                            <strong class="ms-1">
                                <span id="answeredCount">0</span>/<span id="maxCount">{{ $session->max_questions }}</span>
                            </strong>
                        </div>
                    </div>
                </div>
                <div class="progress mt-2" style="height:6px;">
                    <div id="liveProgress" class="progress-bar" style="width:0%"></div>
                </div>
            </div>
        </div>

        <div class="card" id="questionCard">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span id="domainChip" class="badge bg-primary">Loading…</span>
                    <span id="questionSource" class="text-muted small"></span>
                </div>

                <h4 id="questionText" class="mb-4">Preparing your first question…</h4>

                <div id="answerArea"></div>

                <div id="analysisHint" class="mt-3 small text-muted"></div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" id="skipBtn" class="btn btn-outline-secondary" disabled>
                        <i class="fas fa-forward me-2"></i>Skip
                    </button>
                    <button type="button" id="submitBtn" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-paper-plane me-2"></i>Submit Answer
                    </button>
                </div>
            </div>
        </div>

        <div id="crisisBanner" class="alert alert-danger d-none mt-3">
            <h6 class="mb-1"><i class="fas fa-triangle-exclamation me-2"></i>We're here for you</h6>
            <p class="mb-0 small">Crisis line: <strong>+263 717 923 021</strong></p>
        </div>

        <div id="reportBox" class="alert alert-success d-none mt-3">
            <h6 class="mb-2"><i class="fas fa-check-circle me-2"></i>Assessment complete!</h6>
            <a id="reportLink" href="#" class="btn btn-primary btn-sm">
                <i class="fas fa-file-medical me-2"></i>View Report
            </a>
        </div>

        <div id="errorBox" class="alert alert-warning d-none mt-3"></div>

    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // -------- Config --------
    var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var ROUTES = {
        next:   "{{ route('assessment.session.next', $session) }}",
        answer: "{{ route('assessment.session.answer', $session) }}",
        finish: "{{ route('assessment.session.finish', $session) }}"
    };

    // -------- State --------
    var currentQuestion = null;
    var startedAt = Date.now();
    var submitting = false;

    // -------- Elements --------
    var elDomainChip   = document.getElementById('domainChip');
    var elQuestionText = document.getElementById('questionText');
    var elSource       = document.getElementById('questionSource');
    var elAnswerArea   = document.getElementById('answerArea');
    var elHint         = document.getElementById('analysisHint');
    var elSubmitBtn    = document.getElementById('submitBtn');
    var elSkipBtn      = document.getElementById('skipBtn');
    var elAnswered     = document.getElementById('answeredCount');
    var elMax          = document.getElementById('maxCount');
    var elProgress     = document.getElementById('liveProgress');
    var elRiskPill     = document.getElementById('liveRiskPill');
    var elCrisis       = document.getElementById('crisisBanner');
    var elReportBox    = document.getElementById('reportBox');
    var elReportLink   = document.getElementById('reportLink');
    var elErrorBox     = document.getElementById('errorBox');
    var elCard         = document.getElementById('questionCard');

    // -------- Fetch --------
    function api(url, opts) {
        opts = opts || {};
        return fetch(url, {
            method: opts.method || 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: opts.body ? JSON.stringify(opts.body) : undefined,
            credentials: 'same-origin'
        }).then(function (res) {
            if (!res.ok) {
                return res.text().then(function (text) {
                    throw new Error('HTTP ' + res.status + ': ' + text.substring(0, 200));
                });
            }
            return res.json();
        });
    }

    function showError(msg) {
        elErrorBox.textContent = msg;
        elErrorBox.classList.remove('d-none');
    }
    function hideError() {
        elErrorBox.classList.add('d-none');
    }

    function enableSubmit() {
        // console.log('Enabling submit button');
        elSubmitBtn.disabled = false;
    }
    function disableSubmit() {
        elSubmitBtn.disabled = true;
    }

    // -------- Load next question --------
    function loadNext() {
        hideError();
        disableSubmit();
        elSkipBtn.disabled = true;
        elQuestionText.textContent = 'Loading next question…';
        elAnswerArea.innerHTML = '';

        api(ROUTES.next)
            .then(function (data) {
                if (data.done) {
                    finishSession();
                    return;
                }
                currentQuestion = data.question;
                renderQuestion(data);
                updateProgress(data.progress);
            })
            .catch(function (err) {
                console.error(err);
                showError('Failed to load next question: ' + err.message);
            });
    }

    // -------- Render question --------
    function renderQuestion(data) {
        var q = data.question;

        elDomainChip.textContent = q.domain_label || q.domain || 'General';
        elQuestionText.textContent = q.text;
        elSource.textContent = 'source: ' + (q.source || 'template');

        elAnswerArea.innerHTML = '';

        if (q.type === 'text') {
            var ta = document.createElement('textarea');
            ta.id = 'textAnswer';
            ta.className = 'form-control';
            ta.rows = 4;
            ta.placeholder = 'Type your response here…';
            ta.addEventListener('input', function () {
                if (ta.value.trim().length >= 1) {
                    enableSubmit();
                } else {
                    disableSubmit();
                }
            });
            elAnswerArea.appendChild(ta);
            disableSubmit();
            ta.focus();
        } else {
            var options = q.options || [];
            options.forEach(function (opt, i) {
                var wrap = document.createElement('div');
                wrap.className = 'form-check mb-2 p-3 border rounded';
                wrap.style.cursor = 'pointer';
                wrap.style.userSelect = 'none';

                var input = document.createElement('input');
                input.type = 'radio';
                input.className = 'form-check-input';
                input.name = 'qopt';
                input.value = i;
                input.id = 'opt_' + i;

                var label = document.createElement('label');
                label.className = 'form-check-label w-100';
                label.setAttribute('for', 'opt_' + i);
                label.style.cursor = 'pointer';
                label.textContent = opt;

                wrap.appendChild(input);
                wrap.appendChild(label);

                // Click anywhere on the row selects the option
                wrap.addEventListener('click', function (e) {
                    e.preventDefault();
                    input.checked = true;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // When selected, highlight + enable submit
                input.addEventListener('change', function () {
                    elAnswerArea.querySelectorAll('.form-check').forEach(function (el) {
                        el.classList.remove('border-primary', 'bg-light');
                    });
                    wrap.classList.add('border-primary', 'bg-light');
                    enableSubmit();
                });

                elAnswerArea.appendChild(wrap);
            });
            disableSubmit();
        }

        startedAt = Date.now();
        elSkipBtn.disabled = false;
    }

    // -------- Submit answer --------
    function submitAnswer() {
        if (submitting || !currentQuestion) return;
        if (elSubmitBtn.disabled) return;

        submitting = true;
        hideError();

        var originalHtml = elSubmitBtn.innerHTML;
        disableSubmit();
        elSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Analyzing…';

        var payload = {
            question_id: currentQuestion.id,
            latency_ms: Date.now() - startedAt
        };

        if (currentQuestion.type === 'text') {
            var ta = document.getElementById('textAnswer');
            payload.answer = ta ? ta.value.trim() : '';
        } else {
            var sel = elAnswerArea.querySelector('input[name="qopt"]:checked');
            if (!sel) {
                submitting = false;
                elSubmitBtn.innerHTML = originalHtml;
                enableSubmit();
                showError('Please select an option.');
                return;
            }
            payload.choice_index = parseInt(sel.value, 10);
            payload.answer = currentQuestion.options[payload.choice_index];
        }

        api(ROUTES.answer, { method: 'POST', body: payload })
            .then(function (res) {
                if (res.analysis && (res.analysis.crisis || res.analysis.self_harm)) {
                    elCrisis.classList.remove('d-none');
                }
                updateLive(res);
                showHint(res.analysis);
                submitting = false;
                elSubmitBtn.innerHTML = originalHtml;
                loadNext();
            })
            .catch(function (err) {
                console.error(err);
                submitting = false;
                elSubmitBtn.innerHTML = originalHtml;
                enableSubmit();
                showError('Failed to submit: ' + err.message);
            });
    }

    // -------- Finish --------
    function finishSession() {
        api(ROUTES.finish, { method: 'POST' })
            .then(function (res) {
                elCard.classList.add('d-none');
                elReportBox.classList.remove('d-none');
                elReportLink.href = res.redirect;
            })
            .catch(function (err) {
                console.error(err);
                showError('Failed to finalize session: ' + err.message);
            });
    }

    // -------- UI helpers --------
    function updateProgress(p) {
        if (!p) return;
        elAnswered.textContent = p.asked;
        elMax.textContent = p.max;
        var pct = p.max ? Math.min(100, (p.asked / p.max) * 100) : 0;
        elProgress.style.width = pct + '%';
    }

    function updateLive(res) {
        if (res.session) {
            var s = res.session;
            elAnswered.textContent = s.answered;
            elMax.textContent = s.max;
            var pct = s.max ? Math.min(100, (s.answered / s.max) * 100) : 0;
            elProgress.style.width = pct + '%';

            var level = s.risk_level || 'low';
            elRiskPill.className = 'risk-badge risk-' + level + ' ms-1';
            elRiskPill.textContent = level.charAt(0).toUpperCase() + level.slice(1);
        }
    }

    function showHint(a) {
        if (!a) return;
        var bits = [];
        if (a.sentiment_label) bits.push('sentiment: ' + a.sentiment_label);
        if (a.keywords && a.keywords.length) bits.push('keywords: ' + a.keywords.join(', '));
        if (a.crisis) bits.push('⚠ crisis flag');
        if (a.substance) bits.push('substance flag');
        elHint.textContent = bits.join(' · ');
    }

    // -------- Events --------
    elSubmitBtn.addEventListener('click', function (e) {
        e.preventDefault();
        submitAnswer();
    });

    elSkipBtn.addEventListener('click', function (e) {
        e.preventDefault();
        loadNext();
    });

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            if (!elSubmitBtn.disabled) submitAnswer();
        }
    });

    // -------- Boot --------
    loadNext();
})();
</script>
@endpush