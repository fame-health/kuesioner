@php
    $activeQuestionnaires = $questionnaires ?? collect();
    $publishedAnalyses = $publicAnalyses ?? collect();
    $hasActiveQuestionnaires = $activeQuestionnaires->isNotEmpty();
    $nextDeadline = $activeQuestionnaires
        ->filter(fn ($questionnaire) => filled($questionnaire->expired_at))
        ->sortBy('expired_at')
        ->first();
    $totalQuestions = $activeQuestionnaires->sum('questions_count');
    $totalResponses = $activeQuestionnaires->sum('responses_count');
    $facultyLogo = 'https://fpsi.uin-suska.ac.id/wp-content/uploads/2026/04/LOGO-PSIKOLOGI.png';
    $facultyFooterLogo = 'https://fpsi.uin-suska.ac.id/wp-content/uploads/2026/04/LOGO-PSIKOLOGI-300x70.png';
    $facultyBuilding = 'https://fpsi.uin-suska.ac.id/wp-content/uploads/2021/07/GEDUNG-FAKULTAS-PSI-1-e1632713122708.jpg';
    $facultyWebsite = 'https://fpsi.uin-suska.ac.id/';
    $facultyAddress = 'Panam Jl. HR. Soebrantas No.15, Simpang Baru, Kec. Tambang, Kota Pekanbaru, Riau 28293';
    $facultyPhone = '+1-2534-4456-345';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuisioner Aktif | Fakultas Psikologi UIN Suska Riau</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        :root {
            --bg: #f4f7f2;
            --bg-strong: #eaf2ec;
            --surface: #ffffff;
            --surface-soft: #f7faf7;
            --text: #13231a;
            --muted: #647369;
            --border: #d8e4db;
            --primary: #127a4e;
            --primary-dark: #0a4d33;
            --primary-soft: #e6f6ee;
            --accent: #0c817b;
            --gold: #bf8b1f;
            --shadow-sm: 0 10px 24px rgba(19, 35, 26, 0.07);
            --shadow-md: 0 22px 55px rgba(19, 35, 26, 0.12);
            --radius: 8px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(180deg, rgba(18, 122, 78, 0.11), transparent 430px),
                linear-gradient(90deg, rgba(12, 129, 123, 0.08), transparent 45%),
                var(--bg);
            color: var(--text);
            font-family: Poppins, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body::before {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image:
                linear-gradient(rgba(19, 35, 26, 0.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(19, 35, 26, 0.045) 1px, transparent 1px);
            background-size: 42px 42px;
            content: "";
            mask-image: linear-gradient(180deg, black 0%, transparent 78%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page {
            width: min(100% - 32px, 1180px);
            margin: 0 auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 1px solid rgba(216, 228, 219, 0.74);
            background: rgba(244, 247, 242, 0.86);
            backdrop-filter: blur(16px);
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            min-height: 70px;
        }

        .brand {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            display: flex;
            width: 50px;
            height: 50px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(18, 122, 78, 0.22);
            border-radius: var(--radius);
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(18, 122, 78, 0.11);
            padding: 6px;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-title {
            display: block;
            font-size: 0.98rem;
            font-weight: 800;
            line-height: 1.25;
        }

        .brand-subtitle {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 600;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav a {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            color: #405348;
            font-size: 0.88rem;
            font-weight: 700;
            padding: 0 13px;
            transition: background-color 150ms ease, color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
        }

        .nav a:hover {
            background: rgba(18, 122, 78, 0.08);
            color: var(--primary-dark);
        }

        .menu-toggle {
            display: none;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid rgba(18, 122, 78, 0.24);
            border-radius: var(--radius);
            background: #ffffff;
            color: var(--primary-dark);
            cursor: pointer;
            font: inherit;
            font-size: 0.86rem;
            font-weight: 800;
            padding: 0 13px;
            box-shadow: 0 8px 18px rgba(19, 35, 26, 0.05);
        }

        .menu-toggle-bars {
            display: grid;
            gap: 4px;
            width: 18px;
        }

        .menu-toggle-bars span {
            display: block;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
        }

        .admin-link {
            border: 1px solid rgba(18, 122, 78, 0.24);
            background: #ffffff;
            color: var(--primary-dark) !important;
            box-shadow: 0 8px 18px rgba(19, 35, 26, 0.05);
        }

        .admin-link:hover {
            box-shadow: 0 14px 26px rgba(18, 122, 78, 0.13);
            transform: translateY(-1px);
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
            gap: 20px;
            align-items: stretch;
            padding: 30px 0 24px;
        }

        .hero-main {
            overflow: hidden;
            position: relative;
            min-height: clamp(370px, 52vh, 430px);
            border: 1px solid rgba(255, 255, 255, 0.44);
            border-radius: var(--radius);
            background:
                linear-gradient(135deg, rgba(10, 77, 51, 0.98), rgba(18, 122, 78, 0.94) 58%, rgba(12, 129, 123, 0.9)),
                var(--primary-dark);
            color: #ffffff;
            box-shadow: var(--shadow-md);
        }

        .hero-main::after {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 46%;
            height: 100%;
            background:
                linear-gradient(135deg, transparent 0 30%, rgba(255, 255, 255, 0.08) 30% 31%, transparent 31% 100%),
                repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0 1px, transparent 1px 18px);
            content: "";
            opacity: 0.65;
        }

        .hero-main::before {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(10, 77, 51, 0.96) 0%, rgba(10, 77, 51, 0.84) 46%, rgba(10, 77, 51, 0.5) 100%),
                url('{{ $facultyBuilding }}') center right / cover no-repeat;
            content: "";
        }

        .hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            min-height: inherit;
            flex-direction: column;
            justify-content: center;
            padding: clamp(24px, 4vw, 48px);
        }

        .eyebrow {
            display: inline-flex;
            width: max-content;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.78rem;
            font-weight: 700;
            padding: 8px 12px;
        }

        .eyebrow::before {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #f2d27a;
            content: "";
            box-shadow: 0 0 0 5px rgba(242, 210, 122, 0.14);
        }

        h1 {
            max-width: 800px;
            margin: 16px 0 0;
            font-size: clamp(2.15rem, 4.2vw, 3.85rem);
            font-weight: 900;
            letter-spacing: 0;
            line-height: 1.02;
        }

        .hero-copy {
            max-width: 720px;
            margin: 15px 0 0;
            color: rgba(255, 255, 255, 0.83);
            font-size: clamp(1rem, 2vw, 1.12rem);
            line-height: 1.65;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        .button {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            font-size: 0.92rem;
            font-weight: 800;
            padding: 0 17px;
            transition: box-shadow 150ms ease, transform 150ms ease, filter 150ms ease, border-color 150ms ease;
        }

        .button-primary {
            border: 1px solid var(--primary);
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 14px 28px rgba(18, 122, 78, 0.22);
        }

        .hero-main .button-primary {
            border-color: #ffffff;
            background: #ffffff;
            color: var(--primary-dark);
            box-shadow: none;
        }

        .button-primary:hover,
        .button-secondary:hover {
            transform: translateY(-1px);
        }

        .button-secondary {
            border: 1px solid rgba(255, 255, 255, 0.34);
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .button-light {
            border: 1px solid rgba(18, 122, 78, 0.2);
            background: #ffffff;
            color: var(--primary-dark);
        }

        .button-light:hover {
            border-color: rgba(18, 122, 78, 0.38);
            box-shadow: 0 14px 26px rgba(18, 122, 78, 0.12);
            transform: translateY(-1px);
        }

        .hero-side {
            display: grid;
            gap: 14px;
        }

        .status-panel {
            border: 1px solid rgba(18, 122, 78, 0.16);
            border-radius: var(--radius);
            background: #ffffff;
            box-shadow: var(--shadow-sm);
            padding: 16px;
        }

        .status-panel.featured {
            display: grid;
            align-content: space-between;
            min-height: 164px;
            background:
                linear-gradient(180deg, #ffffff, #f8fbf8);
        }

        .panel-label {
            display: block;
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .panel-number {
            display: block;
            margin-top: 10px;
            color: var(--primary-dark);
            font-size: 2.55rem;
            font-weight: 900;
            line-height: 1;
        }

        .panel-copy {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .mini-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .mini-stat {
            border: 1px solid rgba(18, 122, 78, 0.16);
            border-radius: var(--radius);
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(19, 35, 26, 0.05);
            padding: 16px;
        }

        .mini-stat strong {
            display: block;
            color: var(--primary-dark);
            font-size: 1.65rem;
            line-height: 1;
        }

        .mini-stat span {
            display: block;
            margin-top: 7px;
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .deadline-card {
            border-left: 4px solid var(--gold);
        }

        .section {
            padding: 30px 0;
            scroll-margin-top: 86px;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            padding-top: 22px;
            border-top: 1px solid rgba(18, 122, 78, 0.14);
        }

        .section-kicker {
            display: inline-flex;
            margin-bottom: 9px;
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .section-heading h2 {
            margin: 0;
            font-size: clamp(1.45rem, 2.8vw, 2.2rem);
            line-height: 1.16;
        }

        .section-heading p {
            max-width: 610px;
            margin: 9px 0 0;
            color: var(--muted);
            line-height: 1.68;
        }

        .section-badge {
            display: inline-flex;
            flex: 0 0 auto;
            border: 1px solid rgba(18, 122, 78, 0.16);
            border-radius: 999px;
            background: #ffffff;
            color: var(--primary-dark);
            font-size: 0.82rem;
            font-weight: 800;
            padding: 9px 13px;
            box-shadow: 0 8px 18px rgba(19, 35, 26, 0.04);
        }

        .questionnaire-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-top: 22px;
        }

        .questionnaire-card {
            display: flex;
            position: relative;
            min-height: 342px;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: 0 12px 30px rgba(19, 35, 26, 0.06);
            padding: 18px;
            transition: border-color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
        }

        .questionnaire-card::before {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--gold));
            content: "";
        }

        .questionnaire-card:hover {
            border-color: rgba(18, 122, 78, 0.36);
            box-shadow: 0 22px 46px rgba(18, 122, 78, 0.13);
            transform: translateY(-3px);
        }

        .card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-top: 4px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 7px 10px;
            white-space: nowrap;
        }

        .status-pill::before {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--primary);
            content: "";
        }

        .card-code {
            display: grid;
            width: 44px;
            height: 44px;
            flex: 0 0 auto;
            place-items: center;
            border: 1px solid rgba(18, 122, 78, 0.14);
            border-radius: var(--radius);
            background: var(--surface-soft);
            color: var(--primary-dark);
            font-weight: 900;
        }

        .questionnaire-card h3 {
            margin: 19px 0 0;
            font-size: 1.15rem;
            line-height: 1.38;
        }

        .questionnaire-card p {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 0.93rem;
            line-height: 1.68;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 18px;
        }

        .meta-item {
            border: 1px solid #e5ece6;
            border-radius: var(--radius);
            background: #fbfdfb;
            padding: 10px;
        }

        .meta-label {
            display: block;
            color: var(--muted);
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .meta-value {
            display: block;
            min-width: 0;
            margin-top: 4px;
            color: #25352b;
            font-size: 0.87rem;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: auto;
            padding-top: 19px;
        }

        .deadline {
            min-width: 0;
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 700;
            line-height: 1.4;
        }

        .deadline strong {
            display: block;
            color: var(--text);
            font-size: 0.88rem;
            overflow-wrap: anywhere;
        }

        .empty-state {
            display: grid;
            gap: 13px;
            margin-top: 22px;
            border: 1px dashed rgba(18, 122, 78, 0.34);
            border-radius: var(--radius);
            background: rgba(255, 255, 255, 0.78);
            box-shadow: 0 12px 30px rgba(19, 35, 26, 0.04);
            padding: clamp(22px, 4vw, 34px);
        }

        .empty-state h3 {
            margin: 0;
            font-size: 1.24rem;
        }

        .empty-state p {
            max-width: 780px;
            margin: 0;
            color: var(--muted);
            line-height: 1.68;
        }

        .analysis-list {
            display: grid;
            gap: 18px;
            margin-top: 22px;
        }

        .analysis-group {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: #ffffff;
            box-shadow: var(--shadow-sm);
        }

        .analysis-group-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(90deg, var(--primary-soft), #ffffff);
            padding: 18px;
        }

        .analysis-group-head h3 {
            margin: 0;
            color: var(--primary-dark);
            font-size: 1.08rem;
        }

        .analysis-group-head p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 0.85rem;
        }

        .analysis-question-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            padding: 18px;
        }

        .analysis-question {
            display: grid;
            align-content: start;
            gap: 14px;
            border: 1px solid #e5ece6;
            border-radius: var(--radius);
            background: #fbfdfb;
            padding: 16px;
        }

        .analysis-question:only-child {
            grid-column: 1 / -1;
        }

        .analysis-question h4 {
            margin: 0;
            font-size: 0.96rem;
            line-height: 1.5;
        }

        .analysis-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 8px;
        }

        .analysis-pill {
            display: inline-flex;
            min-height: 27px;
            align-items: center;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-size: 0.72rem;
            font-weight: 800;
            padding: 0 9px;
        }

        .analysis-options {
            display: grid;
            gap: 12px;
        }

        .analysis-option {
            display: grid;
            gap: 6px;
        }

        .analysis-option-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            color: #405348;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .analysis-option-head span:last-child {
            flex: 0 0 auto;
            color: var(--primary-dark);
            font-weight: 800;
        }

        .analysis-track {
            height: 9px;
            overflow: hidden;
            border-radius: 999px;
            background: #e7eee9;
        }

        .analysis-bar {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-top: 22px;
        }

        .step-card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(19, 35, 26, 0.05);
            padding: 20px;
        }

        .step-number {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: var(--radius);
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-weight: 900;
        }

        .step-card h3 {
            margin: 15px 0 0;
            font-size: 1.02rem;
        }

        .step-card p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.65;
        }

        .site-footer {
            margin-top: 30px;
            border-top: 1px solid rgba(18, 122, 78, 0.14);
            background:
                linear-gradient(135deg, rgba(10, 77, 51, 0.97), rgba(18, 122, 78, 0.94)),
                var(--primary-dark);
            color: rgba(255, 255, 255, 0.74);
            padding: 36px 0 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: minmax(280px, 1.2fr) repeat(3, minmax(160px, 0.7fr));
            gap: 26px;
            align-items: start;
            padding-bottom: 30px;
        }

        .footer-about {
            display: grid;
            gap: 16px;
            max-width: 420px;
        }

        .footer-about p {
            margin: 0;
            color: var(--muted);
            color: rgba(255, 255, 255, 0.74);
            font-size: 0.92rem;
            line-height: 1.7;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .footer-logo {
            width: 150px;
            max-width: 42vw;
            border-radius: var(--radius);
            background: #ffffff;
            padding: 8px;
            box-shadow: 0 10px 22px rgba(19, 35, 26, 0.07);
        }

        .footer-logo img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .footer-title {
            display: block;
            color: #ffffff;
            font-weight: 800;
        }

        .footer-subtitle {
            display: block;
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.66);
            font-size: 0.85rem;
        }

        .footer-column h3 {
            margin: 0 0 13px;
            color: #ffffff;
            font-size: 0.98rem;
        }

        .footer-list {
            display: grid;
            gap: 9px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .footer-list li,
        .footer-list a {
            color: rgba(255, 255, 255, 0.74);
            font-size: 0.9rem;
            line-height: 1.55;
        }

        .footer-list a {
            transition: color 150ms ease, padding-left 150ms ease;
        }

        .footer-list a:hover {
            color: #ffffff;
            padding-left: 3px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.13);
            padding: 17px 0;
        }

        .footer-bottom-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            color: rgba(255, 255, 255, 0.64);
            font-size: 0.85rem;
        }

        .footer-bottom-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-bottom-links a {
            color: rgba(255, 255, 255, 0.78);
            font-weight: 700;
        }

        @media (min-width: 1001px) and (max-height: 780px) {
            .header-inner {
                min-height: 64px;
            }

            .brand-logo {
                width: 44px;
                height: 44px;
            }

            .brand-title {
                font-size: 0.9rem;
            }

            .brand-subtitle,
            .nav a {
                font-size: 0.78rem;
            }

            .nav a {
                min-height: 36px;
                padding: 0 11px;
            }

            .hero {
                gap: 16px;
                padding: 20px 0 18px;
            }

            .hero-main {
                min-height: 350px;
            }

            .hero-content {
                padding: 30px 34px;
            }

            .eyebrow {
                padding: 6px 10px;
                font-size: 0.72rem;
            }

            h1 {
                max-width: 690px;
                font-size: clamp(2rem, 3.5vw, 3.25rem);
            }

            .hero-copy {
                max-width: 660px;
                font-size: 0.94rem;
                line-height: 1.58;
            }

            .hero-actions {
                margin-top: 18px;
            }

            .button {
                min-height: 40px;
                font-size: 0.84rem;
            }

            .status-panel,
            .mini-stat {
                padding: 14px;
            }

            .status-panel.featured {
                min-height: 140px;
            }

            .panel-number {
                font-size: 2.18rem;
            }

            .panel-copy {
                font-size: 0.82rem;
                line-height: 1.48;
            }

            .mini-stat strong {
                font-size: 1.44rem;
            }

            .mini-stat span {
                font-size: 0.76rem;
            }
        }

        @media (max-width: 1000px) {
            .hero,
            .questionnaire-grid,
            .analysis-question-grid,
            .steps-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .hero-main {
                min-height: 0;
            }

            .questionnaire-card {
                min-height: 0;
            }
        }

        @media (max-width: 720px) {
            .page {
                width: min(100% - 22px, 1180px);
            }

            .site-header {
                position: sticky;
            }

            .header-inner {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 10px;
                min-height: auto;
                padding: 10px 0;
            }

            .brand {
                min-width: 0;
            }

            .brand-logo {
                width: 46px;
                height: 46px;
            }

            .brand-title {
                font-size: 0.9rem;
            }

            .brand-subtitle {
                font-size: 0.76rem;
            }

            .menu-toggle {
                display: inline-flex;
                align-self: center;
            }

            .nav {
                display: none;
                grid-column: 1 / -1;
                width: 100%;
                border: 1px solid rgba(18, 122, 78, 0.15);
                border-radius: var(--radius);
                background: #ffffff;
                box-shadow: 0 14px 32px rgba(19, 35, 26, 0.08);
                padding: 8px;
            }

            .nav.is-open {
                display: grid;
                gap: 8px;
            }

            .nav a {
                width: 100%;
                justify-content: flex-start;
                background: var(--surface-soft);
                padding: 0 12px;
            }

            .section-heading,
            .card-footer,
            .analysis-group-head,
            .footer-brand,
            .footer-bottom-inner {
                align-items: stretch;
                flex-direction: column;
            }

            .hero {
                gap: 14px;
                padding: 20px 0 18px;
            }

            .hero-main {
                border-radius: var(--radius);
            }

            .hero-main::after {
                opacity: 0.28;
            }

            .hero-main::before {
                background:
                    linear-gradient(180deg, rgba(10, 77, 51, 0.94), rgba(10, 77, 51, 0.82)),
                    url('{{ $facultyBuilding }}') center / cover no-repeat;
            }

            .hero-content {
                padding: 26px 20px;
            }

            .section {
                scroll-margin-top: 78px;
            }

            h1 {
                font-size: clamp(2rem, 11vw, 3.05rem);
            }

            .hero-copy {
                font-size: 0.95rem;
            }

            .hero-actions .button,
            .card-footer .button {
                width: 100%;
            }

            .mini-stats,
            .meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<header class="site-header">
    <div class="page header-inner">
        <a class="brand" href="{{ url('/') }}" aria-label="Beranda Fakultas Psikologi UIN Suska Riau">
            <span class="brand-logo">
                <img src="{{ $facultyLogo }}" alt="Logo Fakultas Psikologi UIN Suska Riau">
            </span>
            <span>
                <span class="brand-title">Fakultas Psikologi</span>
                <span class="brand-subtitle">UIN Suska Riau</span>
            </span>
        </a>

        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu">
            <span>Menu</span>
            <span class="menu-toggle-bars" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>

        <nav id="mobile-menu" class="nav" aria-label="Navigasi utama">
            <a href="#kuisioner-aktif">Kuisioner</a>
            @if ($publishedAnalyses->isNotEmpty())
                <a href="#hasil-analisis">Hasil Analisis</a>
            @endif
            <a href="#panduan">Panduan</a>
            <a class="admin-link" href="{{ url('/admin') }}">Masuk Admin</a>
        </nav>
    </div>
</header>

<main class="page">
    <section class="hero" aria-labelledby="page-title">
        <div class="hero-main">
            <div class="hero-content">
                <span class="eyebrow">Portal kuisioner aktif</span>
                <h1 id="page-title">Kuisioner Fakultas Psikologi UIN Suska Riau</h1>
                <p class="hero-copy">
                    Temukan dan isi kuisioner yang sedang dibuka. Seluruh card di halaman ini ditarik langsung
                    dari database, sehingga responden hanya melihat kuisioner yang aktif dan masih tersedia.
                </p>

                <div class="hero-actions">
                    <a class="button button-primary" href="#kuisioner-aktif">Lihat Kuisioner</a>
                    <a class="button button-secondary" href="#panduan">Cara Mengisi</a>
                </div>
            </div>
        </div>

        <aside class="hero-side" aria-label="Ringkasan data kuisioner">
            <div class="status-panel featured">
                <div>
                    <span class="panel-label">Kuisioner aktif</span>
                    <span class="panel-number">{{ $activeQuestionnaires->count() }}</span>
                    <p class="panel-copy">Kuisioner dengan status aktif dan belum melewati batas waktu pengisian.</p>
                </div>
            </div>

            <div class="mini-stats">
                <div class="mini-stat">
                    <strong>{{ $totalQuestions }}</strong>
                    <span>Total pertanyaan</span>
                </div>
                <div class="mini-stat">
                    <strong>{{ $totalResponses }}</strong>
                    <span>Respons terkumpul</span>
                </div>
            </div>

            <div class="status-panel deadline-card">
                <span class="panel-label">Deadline terdekat</span>
                <p class="panel-copy">
                    {{ $nextDeadline?->expired_at?->format('d M Y') ?? 'Tidak ada batas waktu aktif saat ini.' }}
                </p>
            </div>
        </aside>
    </section>

    <section id="kuisioner-aktif" class="section" aria-labelledby="active-title">
        <div class="section-heading">
            <div>
                <span class="section-kicker">Data database</span>
                <h2 id="active-title">Kuisioner yang sedang aktif</h2>
                <p>
                    Daftar ini hanya menampilkan kuisioner aktif. Jika kuisioner tidak muncul, periksa status aktif
                    dan tanggal kedaluwarsa pada dashboard admin.
                </p>
            </div>
            <span class="section-badge">{{ $activeQuestionnaires->count() }} tersedia</span>
        </div>

        @if ($hasActiveQuestionnaires)
            <div class="questionnaire-grid">
                @foreach ($activeQuestionnaires as $questionnaire)
                    <article class="questionnaire-card">
                        <div class="card-top">
                            <span class="card-code">Q{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="status-pill">Aktif</span>
                        </div>

                        <h3>{{ $questionnaire->title }}</h3>
                        <p>
                            {{ filled($questionnaire->description)
                                ? \Illuminate\Support\Str::limit(strip_tags($questionnaire->description), 145)
                                : 'Kuisioner ini sedang terbuka untuk responden Fakultas Psikologi UIN Suska Riau.' }}
                        </p>

                        <div class="meta-grid">
                            <div class="meta-item">
                                <span class="meta-label">Pertanyaan</span>
                                <span class="meta-value">{{ $questionnaire->questions_count }} item</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Respons</span>
                                <span class="meta-value">{{ $questionnaire->responses_count }} masuk</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Dibuat oleh</span>
                                <span class="meta-value">{{ $questionnaire->user?->name ?? 'Admin' }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Dibuka</span>
                                <span class="meta-value">{{ $questionnaire->created_at?->format('d M Y') ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="card-footer">
                            <span class="deadline">
                                Batas waktu
                                <strong>{{ $questionnaire->expired_at?->format('d M Y') ?? 'Tidak dibatasi' }}</strong>
                            </span>
                            <a class="button button-primary" href="{{ $questionnaire->publicUrl() }}">Isi Kuisioner</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <h3>Belum ada kuisioner aktif</h3>
                <p>
                    Halaman ini tidak menampilkan data contoh. Aktifkan kuisioner dari dashboard admin agar card otomatis muncul
                    di bagian ini. Pastikan status kuisioner aktif dan tanggal kedaluwarsa belum lewat.
                </p>
                <div>
                    <a class="button button-light" href="{{ url('/admin') }}">Buka Dashboard Admin</a>
                </div>
            </div>
        @endif
    </section>

    @if ($publishedAnalyses->isNotEmpty())
        <section id="hasil-analisis" class="section" aria-labelledby="analysis-title">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">Ringkasan respons</span>
                    <h2 id="analysis-title">Hasil analisis jawaban</h2>
                    <p>
                        Hasil berikut dipublikasikan oleh admin dan dihitung otomatis dari jawaban berbentuk pilihan.
                    </p>
                </div>
                <span class="section-badge">{{ $publishedAnalyses->sum(fn ($item) => count($item['questions'])) }} pertanyaan</span>
            </div>

            <div class="analysis-list">
                @foreach ($publishedAnalyses as $publishedAnalysis)
                    <article class="analysis-group">
                        <div class="analysis-group-head">
                            <div>
                                <h3>{{ $publishedAnalysis['questionnaire']->title }}</h3>
                                <p>Data diperbarui otomatis saat respons baru masuk.</p>
                            </div>
                            <span class="section-badge">{{ count($publishedAnalysis['questions']) }} analisis</span>
                        </div>

                        <div class="analysis-question-grid">
                            @foreach ($publishedAnalysis['questions'] as $analysis)
                                <section class="analysis-question">
                                    <div>
                                        <h4>{{ $analysis['question'] }}</h4>
                                        <div class="analysis-meta">
                                            <span class="analysis-pill">{{ $analysis['type'] }}</span>
                                            <span class="analysis-pill">{{ $analysis['answered'] }} jawaban</span>
                                            @if ($analysis['is_multiple'])
                                                <span class="analysis-pill">Bisa pilih lebih dari satu</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="analysis-options">
                                        @foreach ($analysis['options'] as $option)
                                            <div class="analysis-option">
                                                <div class="analysis-option-head">
                                                    <span>{{ $option['label'] }}</span>
                                                    <span>{{ $option['count'] }} respons · {{ number_format($option['percentage'], 1, ',', '.') }}%</span>
                                                </div>
                                                <div class="analysis-track">
                                                    <div
                                                        class="analysis-bar"
                                                        style="width: {{ min(100, $option['percentage']) }}%;"
                                                    ></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section id="panduan" class="section" aria-labelledby="guide-title">
        <div class="section-heading">
            <div>
                <span class="section-kicker">Alur responden</span>
                <h2 id="guide-title">Panduan pengisian</h2>
                <p>Ikuti alur singkat berikut agar respons kuisioner tersimpan dengan benar.</p>
            </div>
            <span class="section-badge">3 langkah</span>
        </div>

        <div class="steps-grid">
            <article class="step-card">
                <span class="step-number">1</span>
                <h3>Pilih kuisioner</h3>
                <p>Buka card kuisioner sesuai instruksi penyelenggara atau kebutuhan pengisian.</p>
            </article>
            <article class="step-card">
                <span class="step-number">2</span>
                <h3>Isi jawaban</h3>
                <p>Lengkapi pertanyaan wajib dan periksa kembali jawaban sebelum dikirim.</p>
            </article>
            <article class="step-card">
                <span class="step-number">3</span>
                <h3>Kirim respons</h3>
                <p>Sistem akan menyimpan respons dan menampilkan halaman berhasil setelah formulir dikirim.</p>
            </article>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="page footer-grid">
        <div class="footer-about">
            <div class="footer-brand">
                <span class="footer-logo">
                    <img src="{{ $facultyFooterLogo }}" alt="Logo Fakultas Psikologi UIN Suska Riau">
                </span>
                <div>
                    <span class="footer-title">Fakultas Psikologi UIN Suska Riau</span>
                    <span class="footer-subtitle">Portal kuisioner publik fakultas</span>
                </div>
            </div>
            <p>
                Portal ini membantu responden mengakses kuisioner aktif Fakultas Psikologi UIN Sultan Syarif Kasim Riau.
                Data kuisioner yang tampil berasal langsung dari database aplikasi.
            </p>
        </div>

        <div class="footer-column">
            <h3>Kontak Fakultas</h3>
            <ul class="footer-list">
                <li>{{ $facultyAddress }}</li>
                <li>Telepon: {{ $facultyPhone }}</li>
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Website resmi FPSI</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Profil & Akademik</h3>
            <ul class="footer-list">
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Beranda FPSI</a></li>
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Sejarah, Visi & Misi</a></li>
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Struktur Organisasi</a></li>
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Program Studi Sarjana</a></li>
                <li><a href="https://magisterpsikologi.uin-suska.ac.id/" target="_blank" rel="noopener">Program Magister</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Layanan & Info</h3>
            <ul class="footer-list">
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Kurikulum</a></li>
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Kalender Akademik</a></li>
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Laboratorium</a></li>
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Perpustakaan</a></li>
                <li><a href="{{ $facultyWebsite }}" target="_blank" rel="noopener">Info Mahasiswa</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="page footer-bottom-inner">
            <span>Copyright All Right Reserved 2026, Tim Media FPSI.</span>
            <div class="footer-bottom-links">
                <a href="{{ url('/') }}">Beranda</a>
                <a href="#kuisioner-aktif">Kuisioner Aktif</a>
                <a href="{{ url('/admin') }}">Admin</a>
            </div>
        </div>
    </div>
</footer>
<script>
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.querySelector('#mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('is-open');

            menuToggle.setAttribute('aria-expanded', String(isOpen));
        });

        mobileMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('is-open');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }
</script>
</body>
</html>
