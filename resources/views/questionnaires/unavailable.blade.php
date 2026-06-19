<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuisioner tidak tersedia</title>
    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background:
                linear-gradient(135deg, rgba(79, 70, 229, 0.1), transparent 34%),
                linear-gradient(225deg, rgba(180, 83, 9, 0.1), transparent 38%),
                #f8fafc;
            color: #0f172a;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding: 18px;
        }
        .panel {
            width: min(100%, 560px);
            border: 1px solid #dbe3ef;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.14);
            padding: clamp(26px, 5vw, 42px);
            text-align: center;
        }
        .mark {
            display: grid;
            width: 58px;
            height: 58px;
            margin: 0 auto 18px;
            place-items: center;
            border-radius: 18px;
            background: #fff7ed;
            color: #b45309;
            font-size: 2rem;
            font-weight: 900;
        }
        h1 { margin: 0 0 10px; font-size: clamp(1.8rem, 4vw, 2.5rem); line-height: 1.1; }
        p { margin: 0; color: #64748b; line-height: 1.65; }
    </style>
</head>
<body>
    <main class="panel">
        <div class="mark">!</div>
        <h1>Kuisioner tidak tersedia</h1>
        <p>{{ $message }}</p>
    </main>
</body>
</html>
