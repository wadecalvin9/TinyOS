<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 | Page Not Found</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      height: 100vh;
      width: 100%;
      font-family: "Inter", "Poppins", sans-serif;
      background-color: #f8fafc;
      color: #111827;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
      overflow: hidden;
    }

    .container {
      animation: fadeIn 1.2s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      font-size: 8rem;
      font-weight: 700;
      color: #1e3a8a;
      letter-spacing: -2px;
    }

    h2 {
      font-size: 1.8rem;
      font-weight: 600;
      color: #1f2937;
      margin-top: 10px;
    }

    p {
      color: #6b7280;
      font-size: 1rem;
      margin: 20px 0 40px 0;
      max-width: 400px;
    }

    a.button {
      text-decoration: none;
      background-color: #1e3a8a;
      color: white;
      padding: 12px 28px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(30, 58, 138, 0.15);
    }

    a.button:hover {
      background-color: #2563eb;
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
      transform: translateY(-2px);
    }

    .illustration {
      width: 280px;
      height: auto;
      margin-bottom: 30px;
      opacity: 0.9;
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }

    footer {
      position: absolute;
      bottom: 15px;
      font-size: 0.85rem;
      color: #9ca3af;
    }

    @media (max-width: 600px) {
      h1 {
        font-size: 5rem;
      }
      h2 {
        font-size: 1.3rem;
      }
      .illustration {
        width: 200px;
      }
    }
  </style>
</head>
<body>
  <div class="container">

    <h1>404</h1>
    <h2>Page Not Found</h2>
    <p>Sorry, the page you are looking for doesn’t exist or has been moved. Please return to the homepage.</p>
    <a href="/" class="button">Return Home</a>
  </div>

</body>
</html>
