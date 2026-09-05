@php
    use App\Support\EthiopianCalendar;

    $display = fn ($value) => filled($value) ? $value : '';
    $ethDate = fn ($value) => EthiopianCalendar::formatDate($value) ?? '';
    $ethShortDate = function ($value) {
        $ethiopian = EthiopianCalendar::fromGregorian($value);

        if ($ethiopian === null) {
            return '';
        }

        return sprintf('%d/%d/%d', $ethiopian->getDay(), $ethiopian->getMonth(), $ethiopian->getYear());
    };

    $photoUrl = $expense->inmate?->photoUrl();
    $certificateEthDate = $ethDate($expense->certificate_date);
    $releaseEthDate = $ethDate($expense->release_date);
    $signatureEthDate = $ethShortDate($expense->release_date ?: $expense->certificate_date);
    $printedEthDate = $ethShortDate(now());
    $serialNumber = $expense->certificate_number ?: '0011180';
@endphp
<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.expense.export_title', ['name' => $expense->full_name]) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    <style>
        :root {
            --cert-ink: #111827;
            --cert-red: #c62828;
            --cert-border: #1f2937;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef2f7;
            color: var(--cert-ink);
            font-family: "Noto Sans Ethiopic", "Nyala", "Abyssinica SIL", serif;
            line-height: 1.45;
        }

        .cert-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid #dbe3ef;
            backdrop-filter: blur(8px);
        }

        .cert-toolbar button,
        .cert-toolbar a {
            appearance: none;
            border: 1px solid #cbd5e1;
            border-radius: 0.65rem;
            background: #fff;
            color: #1e293b;
            cursor: pointer;
            font: inherit;
            font-size: 0.92rem;
            padding: 0.55rem 1rem;
            text-decoration: none;
        }

        .cert-toolbar button.primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .cert-page-wrap {
            display: flex;
            justify-content: center;
            padding: 1.5rem 1rem 2.5rem;
        }

        .cert-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
            position: relative;
        }

        .cert-frame {
            margin: 10mm;
            min-height: calc(297mm - 20mm);
            border: 2px solid var(--cert-border);
            outline: 1px solid var(--cert-border);
            outline-offset: 3px;
            padding: 8mm 10mm 10mm;
            position: relative;
        }

        .cert-frame::before,
        .cert-frame::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            width: 14px;
            background:
                repeating-linear-gradient(
                    180deg,
                    transparent 0 6px,
                    rgba(17, 24, 39, 0.08) 6px 8px,
                    transparent 8px 14px
                );
            pointer-events: none;
        }

        .cert-frame::before {
            left: -1px;
        }

        .cert-frame::after {
            right: -1px;
        }

        .cert-corner {
            position: absolute;
            width: 22px;
            height: 22px;
            border: 2px solid var(--cert-border);
        }

        .cert-corner-tl { top: -1px; left: -1px; border-right: 0; border-bottom: 0; }
        .cert-corner-tr { top: -1px; right: -1px; border-left: 0; border-bottom: 0; }
        .cert-corner-bl { bottom: -1px; left: -1px; border-right: 0; border-top: 0; }
        .cert-corner-br { bottom: -1px; right: -1px; border-left: 0; border-top: 0; }

        .cert-header {
            display: grid;
            grid-template-columns: 78px 1fr 92px;
            gap: 10px;
            align-items: start;
        }

        .cert-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }

        .cert-org {
            text-align: center;
            padding-top: 2px;
        }

        .cert-org-am {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .cert-org-en {
            margin-top: 2px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .cert-photo-box {
            width: 92px;
            height: 108px;
            border: 1.5px solid var(--cert-border);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fafafa;
        }

        .cert-photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cert-photo-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #475569;
        }

        .cert-meta-row {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: end;
            margin-top: 10px;
            gap: 12px;
        }

        .cert-date-line {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .cert-date-value {
            display: inline-block;
            min-width: 180px;
            border-bottom: 1px solid var(--cert-ink);
            padding: 0 4px 2px;
            margin-inline-start: 6px;
        }

        .cert-serial {
            color: var(--cert-red);
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .cert-title-block {
            margin: 16px 0 14px;
            text-align: center;
        }

        .cert-title-line {
            font-size: 0.98rem;
            font-weight: 600;
            line-height: 1.55;
        }

        .cert-title-main {
            margin-top: 4px;
            font-size: 1.18rem;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .cert-fields {
            display: grid;
            gap: 7px;
        }

        .cert-row {
            display: grid;
            gap: 10px;
        }

        .cert-row-split {
            grid-template-columns: 1.35fr 0.65fr;
        }

        .cert-row-pair {
            grid-template-columns: 1fr 1fr;
        }

        .cert-field {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 8px;
            align-items: end;
            font-size: 0.92rem;
        }

        .cert-label {
            white-space: nowrap;
            font-weight: 600;
        }

        .cert-value {
            min-height: 1.35em;
            border-bottom: 1px solid var(--cert-ink);
            padding: 0 2px 2px;
            word-break: break-word;
        }

        .cert-declaration {
            margin-top: 14px;
            font-size: 0.9rem;
            line-height: 1.65;
            text-align: justify;
        }

        .cert-signature-row {
            margin-top: 18px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 10px;
            align-items: end;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .cert-signature-line {
            border-bottom: 1px solid var(--cert-ink);
            min-height: 1.35em;
            padding-bottom: 2px;
        }

        .cert-footer-note {
            margin-top: 18px;
            text-align: end;
            font-size: 0.72rem;
            color: #475569;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                background: #fff;
            }

            .cert-toolbar {
                display: none !important;
            }

            .cert-page-wrap {
                padding: 0;
            }

            .cert-sheet {
                width: auto;
                min-height: auto;
                box-shadow: none;
            }

            .cert-frame {
                margin: 8mm;
                min-height: calc(297mm - 16mm);
            }
        }
    </style>
</head>
<body>
    <div class="cert-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.expense.print') }}</button>
        <button type="button" onclick="window.close()">{{ __('app.expense.close') }}</button>
    </div>

    <div class="cert-page-wrap">
        <article class="cert-sheet">
            <div class="cert-frame">
                <span class="cert-corner cert-corner-tl" aria-hidden="true"></span>
                <span class="cert-corner cert-corner-tr" aria-hidden="true"></span>
                <span class="cert-corner cert-corner-bl" aria-hidden="true"></span>
                <span class="cert-corner cert-corner-br" aria-hidden="true"></span>

                <header class="cert-header">
                    <img src="{{ asset('images/intake-logo.jpeg') }}" alt="" class="cert-logo">

                    <div class="cert-org">
                        <div class="cert-org-am">{{ __('app.expense.form_title') }}</div>
                        <div class="cert-org-en">South Ethiopia Prison Police Commission</div>
                    </div>

                    <div class="cert-photo-box">
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="{{ $expense->full_name }}">
                        @else
                            <span class="cert-photo-label">{{ __('app.expense.fields.photo') }}</span>
                        @endif
                    </div>
                </header>

                <div class="cert-meta-row">
                    <div class="cert-date-line">
                        {{ __('app.expense.fields.certificate_date') }}
                        <span class="cert-date-value">{{ $certificateEthDate }}</span>
                    </div>
                    <div class="cert-serial">№ {{ $serialNumber }}</div>
                </div>

                <div class="cert-title-block">
                    <div class="cert-title-line">
                        {{ __('app.expense.export_heading', ['institute' => $institute]) }}
                    </div>
                    <div class="cert-title-main">{{ __('app.expense.form_certificate_title') }}</div>
                </div>

                <div class="cert-fields">
                    <div class="cert-row cert-row-split">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.full_name') }}</span>
                            <span class="cert-value">{{ $display($expense->full_name) }}</span>
                        </div>
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.age') }}</span>
                            <span class="cert-value">{{ $display($expense->age) }}</span>
                        </div>
                    </div>

                    <div class="cert-row cert-row-pair">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.gender') }}</span>
                            <span class="cert-value">{{ $display($expense->gender) }}</span>
                        </div>
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.religion') }}</span>
                            <span class="cert-value">{{ $display($expense->religion) }}</span>
                        </div>
                    </div>

                    <div class="cert-row cert-row-pair">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.nationality') }}</span>
                            <span class="cert-value">{{ $display($expense->nationality) }}</span>
                        </div>
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.country_of_birth') }}</span>
                            <span class="cert-value">{{ $display($expense->country_of_birth) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.admission_date') }}</span>
                            <span class="cert-value">{{ $ethDate($expense->admission_date) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.sentencing_court') }}</span>
                            <span class="cert-value">{{ $display($expense->sentencing_court) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.sentence_duration') }}</span>
                            <span class="cert-value">{{ $display($expense->sentence_duration) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.crime_type') }}</span>
                            <span class="cert-value">{{ $display($expense->crime_type) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.court_file_number') }}</span>
                            <span class="cert-value">{{ $display($expense->court_file_number) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.institution_id_number') }}</span>
                            <span class="cert-value">{{ $display($expense->institution_id_number) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.education_skill_before') }}</span>
                            <span class="cert-value">{{ $display($expense->education_skill_before) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.learned_in_institution') }}</span>
                            <span class="cert-value">{{ $display($expense->previous_profession) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.conduct_in_institution') }}</span>
                            <span class="cert-value">{{ $display($expense->work_training_provided) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.education_period_provided') }}</span>
                            <span class="cert-value">{{ $display($expense->education_period_provided) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.sentence_served') }}</span>
                            <span class="cert-value">{{ $display($expense->work_experience_during) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.release_date') }}</span>
                            <span class="cert-value">{{ $releaseEthDate }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.release_reason') }}</span>
                            <span class="cert-value">{{ $display($expense->release_reason) }}</span>
                        </div>
                    </div>

                    <div class="cert-row">
                        <div class="cert-field">
                            <span class="cert-label">{{ __('app.expense.fields.health_condition') }}</span>
                            <span class="cert-value">{{ $display($expense->health_condition) }}</span>
                        </div>
                    </div>
                </div>

                <p class="cert-declaration">{{ __('app.expense.signature_confirm') }}</p>

                <div class="cert-signature-row">
                    <span class="cert-signature-line"></span>
                    <span>{{ __('app.expense.export_signature_date', ['date' => $signatureEthDate]) }}</span>
                    <span class="cert-signature-line">{{ $display($expense->signature ?: $expense->official_name) }}</span>
                </div>

                <p class="cert-footer-note">
                    {{ __('app.expense.export_print_note', ['institute' => $institute, 'date' => $printedEthDate]) }}
                </p>
            </div>
        </article>
    </div>

    @if (request()->boolean('print'))
        <script>window.addEventListener('load', () => window.print());</script>
    @endif
</body>
</html>
