@extends('layouts.app')

@section('title', 'Reports')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
    <h2 class="mb-0">
        <i class="fas fa-file-medical-alt me-2"></i>Reports
    </h2>
    <div class="btn-group">
        <a href="{{ route('reports.index', ['type' => 'daily']) }}"
           class="btn btn-sm {{ $report['type'] === 'daily' ? 'btn-primary' : 'btn-outline-primary' }}">Daily</a>
        <a href="{{ route('reports.index', ['type' => 'weekly']) }}"
           class="btn btn-sm {{ $report['type'] === 'weekly' ? 'btn-primary' : 'btn-outline-primary' }}">Weekly</a>
        <a href="{{ route('reports.index', ['type' => 'monthly']) }}"
           class="btn btn-sm {{ $report['type'] === 'monthly' ? 'btn-primary' : 'btn-outline-primary' }}">Monthly</a>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <form method="GET" action="{{ route('reports.index') }}" class="d-flex gap-2">
                    <input type="hidden" name="type" value="custom">
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-filter me-1"></i>Apply
                    </button>
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('reports.export', ['format' => 'pdf', 'type' => $report['type'], 'from' => request('from'), 'to' => request('to')]) }}"
                   class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('reports.export', ['format' => 'csv', 'type' => $report['type'], 'from' => request('from'), 'to' => request('to')]) }}"
                   class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i>Excel
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <i class="fas fa-print me-1"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<div class="d-none d-print-block text-center mb-4">
    <h2>Smart Health Advisor</h2>
    <p class="mb-0">{{ ucfirst($report['type']) }} Report</p>
    <p class="text-muted small">{{ $report['from'] }} → {{ $report['to'] }} · Generated {{ $report['generated'] }}</p>
    <hr>
</div>

{{-- TOTALS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-primary">{{ $report['totals']['queries'] }}</div>
            <div class="stat-label">AI Advisor Qs</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-info">{{ $report['totals']['sessions'] }}</div>
            <div class="stat-label">Assessments</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-success">{{ $report['totals']['taken'] }}</div>
            <div class="stat-label">Doses Taken</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-danger">{{ $report['totals']['missed'] }}</div>
            <div class="stat-label">Doses Missed</div>
        </div>
    </div>
</div>

{{-- SENTIMENT ANALYSIS --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-smile me-2"></i>Sentiment Analysis</span>
        <button id="runSentimentBtn" class="btn btn-sm btn-primary">
            <i class="fas fa-play me-1"></i>Run Sentiment Analysis
        </button>
    </div>
    <div class="card-body">

        <div id="sentimentEmpty" class="text-center text-muted py-4">
            <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>
            Click <strong>Run Sentiment Analysis</strong> to compute the sentiment score of all your activities in this period.
        </div>

        <div id="sentimentLoading" class="text-center py-4 d-none">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 mb-0 text-muted">Analyzing…</p>
        </div>

        <div id="sentimentError" class="alert alert-danger d-none"></div>

        <div id="sentimentResults" class="d-none">

            {{-- Overall --}}
            <div class="row align-items-center mb-4">
                <div class="col-md-4 text-center">
                    <div id="sentimentEmoji" style="font-size:4rem; line-height:1;">😐</div>
                    <h5 id="sentimentLabel" class="mt-2 mb-0">—</h5>
                    <div id="sentimentAverage" class="fs-3 fw-bold">—</div>
                    <small class="text-muted">Overall Sentiment Score</small>
                </div>
                <div class="col-md-8">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="stat-mini">
                                <div id="sentimentPositive" class="stat-value text-success">0</div>
                                <div class="stat-label">Positive</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini">
                                <div id="sentimentNeutral" class="stat-value text-secondary">0</div>
                                <div class="stat-label">Neutral</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-mini">
                                <div id="sentimentNegative" class="stat-value text-danger">0</div>
                                <div class="stat-label">Negative</div>
                            </div>
                        </div>
                    </div>
                    <div id="sentimentSamples" class="text-center text-muted small mt-3"></div>
                </div>
            </div>

            {{-- Breakdown by source --}}
            <h6 class="text-muted mb-3 mt-4"><i class="fas fa-layer-group me-1"></i>Breakdown by Source</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-check fa-2x text-info mb-2"></i>
                            <h6 class="mb-1">Assessments</h6>
                            <div id="srcAssessScore" class="fs-4 fw-bold">—</div>
                            <div id="srcAssessCount" class="small text-muted">— samples</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-stethoscope fa-2x text-primary mb-2"></i>
                            <h6 class="mb-1">AI Advisor Queries</h6>
                            <div id="srcAdvisorScore" class="fs-4 fw-bold">—</div>
                            <div id="srcAdvisorCount" class="small text-muted">— samples</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-medical-alt fa-2x text-success mb-2"></i>
                            <h6 class="mb-1">Assessment Reports</h6>
                            <div id="srcReportScore" class="fs-4 fw-bold">—</div>
                            <div id="srcReportCount" class="small text-muted">— samples</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Per category --}}
            <div id="sentimentCategories" class="mt-4"></div>
        </div>
    </div>
</div>

{{-- ASSESSMENT SESSIONS --}}
@if(!empty($report['sessions']))
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-clipboard-check me-2"></i>Assessment Sessions ({{ count($report['sessions']) }})
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Mode</th>
                    <th>Categories</th>
                    <th>Risk</th>
                    <th>Score</th>
                    <th>Sentiment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['sessions'] as $s)
                <tr>
                    <td class="small">{{ $s['date'] }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($s['mode']) }}</span></td>
                    <td>
                        @foreach($s['categories'] as $c)
                            <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $c)) }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="risk-badge risk-{{ $s['risk_level'] }}">
                            {{ ucfirst($s['risk_level']) }}
                        </span>
                    </td>
                    <td>{{ $s['risk_score'] }}/100</td>
                    <td>{{ number_format($s['sentiment'], 2) }}</td>
                    <td>
                        @if($s['report_id'])
                            <a href="{{ route('assessment.report.show', $s['report_id']) }}" class="btn btn-sm btn-outline-primary">
                                View Report
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- AI ADVISOR QUERIES --}}
@if(!empty($report['advisor']))
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-stethoscope me-2"></i>AI Health Advisor Queries ({{ count($report['advisor']) }})
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Question</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Sentiment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['advisor'] as $q)
                <tr>
                    <td class="small text-muted">{{ $q['date'] }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($q['question'], 70) }}</td>
                    <td>{{ ucfirst($q['category']) }}</td>
                    <td>
                        <span class="badge bg-{{ $q['urgency'] === 'critical' ? 'danger' : ($q['urgency'] === 'high' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($q['urgency']) }}
                        </span>
                    </td>
                    <td>
                        @if($q['sentiment'] > 0.2) 😊
                        @elseif($q['sentiment'] > -0.2) 😐
                        @else 😟 @endif
                        <small>{{ $q['label'] }}</small>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- MEDICATION ADHERENCE --}}
@if($report['totals']['adherence'] !== null)
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-pills me-2"></i>Medication Adherence
    </div>
    <div class="card-body">
        <div class="progress" style="height: 24px;">
            <div class="progress-bar bg-success"
                 style="width: {{ $report['totals']['adherence'] }}%">
                {{ $report['totals']['adherence'] }}%
            </div>
        </div>
        <div class="d-flex justify-content-between mt-2 small">
            <span><strong>{{ $report['totals']['taken'] }}</strong> taken</span>
            <span><strong>{{ $report['totals']['missed'] }}</strong> missed</span>
        </div>
    </div>
</div>
@endif

{{-- CATEGORY + URGENCY BREAKDOWN --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">By Category</div>
            <div class="card-body">
                @forelse($report['by_category'] as $cat => $count)
                    <div class="d-flex justify-content-between border-bottom py-1">
                        <span>{{ ucfirst(str_replace('_', ' ', $cat ?? 'general')) }}</span>
                        <strong>{{ $count }}</strong>
                    </div>
                @empty
                    <p class="text-muted mb-0">No data.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">By Urgency</div>
            <div class="card-body">
                @forelse($report['by_urgency'] as $urg => $count)
                    <div class="d-flex justify-content-between border-bottom py-1">
                        <span>{{ ucfirst($urg) }}</span>
                        <strong>{{ $count }}</strong>
                    </div>
                @empty
                    <p class="text-muted mb-0">No data.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn     = document.getElementById('runSentimentBtn');
    var empty   = document.getElementById('sentimentEmpty');
    var loading = document.getElementById('sentimentLoading');
    var error   = document.getElementById('sentimentError');
    var results = document.getElementById('sentimentResults');
    var CSRF    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    btn.addEventListener('click', function () {
        empty.classList.add('d-none');
        results.classList.add('d-none');
        error.classList.add('d-none');
        loading.classList.remove('d-none');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Analyzing…';

        fetch("{{ route('reports.sentiment') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                type: "{{ $report['type'] }}",
                from: "{{ request('from') }}",
                to:   "{{ request('to') }}"
            })
        })
        .then(function (r) {
            if (!r.ok) {
                return r.text().then(function (t) {
                    throw new Error('HTTP ' + r.status + ': ' + t.substring(0, 150));
                });
            }
            return r.json();
        })
        .then(function (data) {
            loading.classList.add('d-none');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-redo me-1"></i>Re-run Analysis';

            renderCurrent(data.current || data);

            results.classList.remove('d-none');
        })
        .catch(function (err) {
            console.error(err);
            loading.classList.add('d-none');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play me-1"></i>Run Sentiment Analysis';
            error.textContent = 'Failed: ' + err.message;
            error.classList.remove('d-none');
        });
    });

    function renderCurrent(c) {
        var emoji = '😐';
        if (c.average > 0.3) emoji = '😊';
        else if (c.average > 0.05) emoji = '🙂';
        else if (c.average > -0.05) emoji = '😐';
        else if (c.average > -0.3) emoji = '😟';
        else emoji = '😢';

        document.getElementById('sentimentEmoji').textContent = emoji;
        document.getElementById('sentimentLabel').textContent =
            c.label.replace(/_/g, ' ').replace(/\b\w/g, function (x) { return x.toUpperCase(); });
        document.getElementById('sentimentAverage').textContent = c.average;
        document.getElementById('sentimentPositive').textContent = c.positive;
        document.getElementById('sentimentNeutral').textContent = c.neutral;
        document.getElementById('sentimentNegative').textContent = c.negative;
        document.getElementById('sentimentSamples').textContent =
            'Analyzed ' + c.samples + ' total responses in this period.';

        // Source breakdown (if the controller sends it)
        var src = c.by_source || {};
        setSrc('Assess', src.assessments);
        setSrc('Advisor', src.advisor);
        setSrc('Report', src.reports);

        // Per-category breakdown
        renderCategories(c.by_category || {});
    }

    function setSrc(prefix, data) {
        var scoreEl = document.getElementById('src' + prefix + 'Score');
        var countEl = document.getElementById('src' + prefix + 'Count');
        if (!scoreEl || !countEl) return;

        if (!data || data.samples === 0) {
            scoreEl.textContent = '—';
            countEl.textContent = 'no samples';
        } else {
            scoreEl.textContent = data.average;
            countEl.textContent = data.samples + ' samples';
        }
    }

    function renderCategories(cats) {
        var container = document.getElementById('sentimentCategories');
        var keys = Object.keys(cats);

        if (keys.length === 0) {
            container.innerHTML = '';
            return;
        }

        var html = '<h6 class="text-muted mb-3"><i class="fas fa-tags me-1"></i>By Category</h6>';
        html += '<div class="row g-2">';

        keys.forEach(function (cat) {
            var score = cats[cat];
            var emoji = score > 0.2 ? '😊' : (score < -0.2 ? '😟' : '😐');
            html += '<div class="col-md-4 col-sm-6">' +
                '<div class="border rounded p-3 d-flex justify-content-between align-items-center">' +
                    '<div>' +
                        '<div class="fw-semibold">' + cat.replace(/_/g, ' ').replace(/\b\w/g, function (x) { return x.toUpperCase(); }) + '</div>' +
                        '<small class="text-muted">Average score</small>' +
                    '</div>' +
                    '<div class="text-end">' +
                        '<div style="font-size:1.5rem;">' + emoji + '</div>' +
                        '<strong>' + score + '</strong>' +
                    '</div>' +
                '</div>' +
            '</div>';
        });

        html += '</div>';
        container.innerHTML = html;
    }
});
</script>
@endpush

@push('styles')
<style>
    @media print {
        .sidebar, .sidebar-toggle, .sidebar-backdrop,
        .app-footer, .d-print-none, button, .btn-group, a.btn {
            display: none !important;
        }
        .main-wrapper { margin-left: 0 !important; width: 100% !important; }
        .main-content { padding: 0 !important; }
        .card {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
            page-break-inside: avoid;
        }
        body::before { display: none !important; }
        body { background: white !important; }
    }
</style>
@endpush