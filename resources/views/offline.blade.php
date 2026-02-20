<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - LocalSaver</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0a1e 0%, #1e1b4b 50%, #0f0a1e 100%);
            color: white;
            font-family: system-ui, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 1.5rem;
            padding: 2rem;
            text-align: center;
            max-width: 400px;
        }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        p { color: rgba(255,255,255,0.6); margin-bottom: 1.5rem; }
        button {
            background: linear-gradient(90deg, #7c3aed, #9333ea);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="card">
        <h1>You're Offline</h1>
        <p>Please check your connection and try again.</p>
        <button onclick="window.location.reload()">Retry</button>
    </div>
</body>
</html>
