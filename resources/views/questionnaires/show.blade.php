@php
    $questionCount = $questionnaire->questions->count();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $questionnaire->title }}</title>
    <style>
        :root {
            --bg: #eef2ff;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --border: #dbe3ef;
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --teal: #0f766e;
            --amber: #b45309;
            --danger: #dc2626;
            --shadow: 0 24px 70px rgba(15, 23, 42, 0.14);
            --radius: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(135deg, rgba(79, 70, 229, 0.11), transparent 34%),
                linear-gradient(225deg, rgba(15, 118, 110, 0.1), transparent 38%),
                linear-gradient(180deg, #f8fafc 0%, var(--bg) 48%, #f8fafc 100%);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body::before {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.045) 1px, transparent 1px);
            background-size: 44px 44px;
            content: "";
            mask-image: linear-gradient(180deg, black, transparent 82%);
        }

        main {
            width: min(100%, 1040px);
            margin: 0 auto;
            padding: 34px 18px 54px;
        }

        .hero {
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.38);
            border-radius: 24px;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.96), rgba(49, 46, 129, 0.94) 52%, rgba(15, 118, 110, 0.94)),
                #0f172a;
            box-shadow: var(--shadow);
            color: white;
        }

        .hero-inner {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 24px;
            align-items: end;
            padding: clamp(24px, 4vw, 42px);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            padding: 7px 11px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.78rem;
            font-weight: 800;
        }

        h1 {
            max-width: 760px;
            margin: 14px 0 0;
            font-size: clamp(2rem, 5vw, 3.7rem);
            font-weight: 850;
            letter-spacing: 0;
            line-height: 1.02;
            overflow-wrap: anywhere;
        }

        .description {
            max-width: 760px;
            margin: 14px 0 0;
            color: rgba(255, 255, 255, 0.76);
            font-size: 1rem;
            line-height: 1.7;
        }

        .summary {
            display: grid;
            gap: 10px;
            min-width: 184px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.11);
            padding: 16px;
            backdrop-filter: blur(12px);
        }

        .summary strong {
            font-size: 2.2rem;
            line-height: 1;
        }

        .summary span {
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.84rem;
            font-weight: 700;
        }

        .form-shell {
            display: grid;
            gap: 18px;
            margin-top: 20px;
        }

        .card {
            border: 1px solid rgba(219, 227, 239, 0.92);
            border-radius: var(--radius);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
        }

        .respondent-card,
        .question-card,
        .submit-card {
            padding: clamp(18px, 3vw, 26px);
        }

        .section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 850;
        }

        .section-copy {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.55;
        }

        .chip {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            border-radius: 999px;
            background: #ecfeff;
            color: var(--teal);
            padding: 7px 11px;
            font-size: 0.76rem;
            font-weight: 850;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        label,
        .question-title {
            color: #1e293b;
            font-size: 0.96rem;
            font-weight: 800;
            line-height: 1.45;
        }

        .required {
            color: var(--danger);
            font-weight: 900;
        }

        .optional {
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 750;
        }

        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 13px;
            background: var(--surface);
            color: var(--text);
            font: inherit;
            outline: none;
            padding: 13px 14px;
            transition: border-color 150ms ease, box-shadow 150ms ease, background-color 150ms ease;
        }

        textarea {
            min-height: 136px;
            resize: vertical;
        }

        select {
            appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, #64748b 50%),
                linear-gradient(135deg, #64748b 50%, transparent 50%);
            background-position:
                calc(100% - 20px) 50%,
                calc(100% - 14px) 50%;
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
            padding-right: 42px;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.13);
        }

        .question-card {
            display: grid;
            gap: 16px;
        }

        .question-top {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 14px;
            align-items: start;
        }

        .number {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border-radius: 13px;
            background: #eef2ff;
            color: var(--primary-dark);
            font-weight: 900;
        }

        .question-meta {
            border-radius: 999px;
            background: var(--surface-soft);
            color: var(--muted);
            padding: 7px 10px;
            font-size: 0.75rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .choices {
            display: grid;
            gap: 10px;
        }

        .choice {
            position: relative;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            padding: 13px 14px;
            color: #334155;
            cursor: pointer;
            transition: border-color 150ms ease, background-color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
        }

        .choice:hover {
            border-color: #a5b4fc;
            background: #fbfdff;
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        }

        .choice input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .choice:has(input:checked) {
            border-color: rgba(79, 70, 229, 0.55);
            background: #eef2ff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.09);
            color: #1e1b4b;
        }

        .choice span {
            min-width: 0;
            overflow-wrap: anywhere;
            font-weight: 700;
        }

        .error {
            border-radius: 12px;
            background: #fef2f2;
            color: #b91c1c;
            padding: 9px 11px;
            font-size: 0.86rem;
            font-weight: 700;
        }

        .alert {
            border-color: rgba(220, 38, 38, 0.22);
            background: #fff7f7;
            color: #991b1b;
            padding: 16px 18px;
            font-weight: 750;
        }

        .submit-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .submit-copy {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
        }

        button {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--teal));
            box-shadow: 0 16px 30px rgba(79, 70, 229, 0.24);
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 850;
            padding: 0 20px;
            transition: box-shadow 150ms ease, transform 150ms ease, filter 150ms ease;
            white-space: nowrap;
        }

        button:hover {
            filter: brightness(1.04);
            transform: translateY(-1px);
            box-shadow: 0 20px 34px rgba(15, 118, 110, 0.24);
        }

        button:focus-visible {
            outline: 4px solid rgba(79, 70, 229, 0.22);
            outline-offset: 3px;
        }

        @media (max-width: 760px) {
            main {
                padding: 18px 12px 36px;
            }

            .hero-inner,
            .grid-2,
            .submit-card {
                grid-template-columns: 1fr;
            }

            .hero-inner {
                align-items: start;
            }

            .summary {
                min-width: 0;
            }

            .section-head,
            .submit-card {
                display: grid;
            }

            .question-top {
                grid-template-columns: auto minmax(0, 1fr);
            }

            .question-meta {
                grid-column: 1 / -1;
                justify-self: start;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<main>
    <form method="POST" action="{{ route('questionnaires.public.submit', $questionnaire->public_token) }}">
        @csrf

        <header class="hero">
            <div class="hero-inner">
                <div>
                    <span class="eyebrow">Form kuisioner publik</span>
                    <h1>{{ $questionnaire->title }}</h1>

                    @if ($questionnaire->description)
                        <p class="description">{{ $questionnaire->description }}</p>
                    @endif
                </div>

                <div class="summary">
                    <strong>{{ $questionCount }}</strong>
                    <span>pertanyaan untuk dijawab</span>
                </div>
            </div>
        </header>

        <div class="form-shell">
            @if ($errors->any())
                <div class="card alert">
                    Ada beberapa jawaban yang perlu diperiksa lagi.
                </div>
            @endif

            <section class="card respondent-card">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">Identitas responden</h2>
                        <p class="section-copy">Bagian ini opsional, kecuali penyelenggara meminta data kontak Anda.</p>
                    </div>
                    <span class="chip">Opsional</span>
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label for="respondent_name">Nama responden</label>
                        <input id="respondent_name" type="text" name="respondent_name" value="{{ old('respondent_name') }}" autocomplete="name" placeholder="Tulis nama Anda">
                        @error('respondent_name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="respondent_email">Email responden</label>
                        <input id="respondent_email" type="email" name="respondent_email" value="{{ old('respondent_email') }}" autocomplete="email" placeholder="nama@email.com">
                        @error('respondent_email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </section>

            @foreach ($questionnaire->questions as $question)
                <section class="card question-card">
                    <div class="question-top">
                        <div class="number">{{ $loop->iteration }}</div>
                        <div>
                            <div class="label-row">
                                <div class="question-title">
                                    {{ $question->question_text }}
                                    @if ($question->is_required)
                                        <span class="required">*</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="question-meta">
                            {{ $question->is_required ? 'Wajib' : 'Opsional' }}
                        </div>
                    </div>

                    @if ($question->question_type === \App\Models\Question::TYPE_SHORT_TEXT)
                        <div class="field">
                            <input type="text" name="answers[{{ $question->id }}]" value="{{ old('answers.' . $question->id) }}" placeholder="Tulis jawaban singkat">
                        </div>
                    @elseif ($question->question_type === \App\Models\Question::TYPE_PARAGRAPH)
                        <div class="field">
                            <textarea name="answers[{{ $question->id }}]" placeholder="Tulis jawaban Anda dengan jelas">{{ old('answers.' . $question->id) }}</textarea>
                        </div>
                    @elseif ($question->question_type === \App\Models\Question::TYPE_RADIO)
                        <div class="choices">
                            @foreach ($question->options as $option)
                                <label class="choice">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->option_text }}" @checked(old('answers.' . $question->id) === $option->option_text)>
                                    <span>{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif ($question->question_type === \App\Models\Question::TYPE_CHECKBOX)
                        <div class="choices">
                            @foreach ($question->options as $option)
                                <label class="choice">
                                    <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->option_text }}" @checked(in_array($option->option_text, old('answers.' . $question->id, []), true))>
                                    <span>{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif ($question->question_type === \App\Models\Question::TYPE_DROPDOWN)
                        <div class="field">
                            <select name="answers[{{ $question->id }}]">
                                <option value="">Pilih jawaban</option>
                                @foreach ($question->options as $option)
                                    <option value="{{ $option->option_text }}" @selected(old('answers.' . $question->id) === $option->option_text)>{{ $option->option_text }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @error('answers.' . $question->id)
                        <div class="error">{{ $message }}</div>
                    @enderror
                </section>
            @endforeach

            <section class="card submit-card">
                <p class="submit-copy">Pastikan jawaban sudah sesuai sebelum dikirim.</p>
                <button type="submit">Kirim Jawaban</button>
            </section>
        </div>
    </form>
</main>
</body>
</html>
