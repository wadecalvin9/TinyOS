<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Glass Search</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    body {
      margin: 0;
      height: 100vh;
      background-color: rgb(255, 255, 255);
      backdrop-filter: blur(20px);
      color: #080808;
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
    }

    .header {
      background: rgba(255, 255, 255, 0.1);
      padding: 10px 15px;
      font-weight: 600;
      letter-spacing: 1px;
      text-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
      border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }

    .browser-toolbar {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 12px;
      background: rgba(255, 255, 255, 0.08);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .browser-toolbar i {
      cursor: pointer;
      color: rgb(0, 0, 0);
      padding: 6px;
      border-radius: 6px;
      transition: 0.2s;
    }

    .browser-toolbar i:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #38bdf8;
    }

    .searchbox {
      flex: 1;
      background: rgba(255, 255, 255, 0.07);
      border-radius: 10px;
      margin: 10px;
      padding: 10px;
      overflow-y: auto;
    }

    .gcse-search {
      width: 100%;
    }
  </style>
</head>

<body>
  <div class="header">gLASS Search</div>
  <div class="browser-toolbar">
    <i class="fa-solid fa-arrow-left"></i>
    <i class="fa-solid fa-arrow-right"></i>
    <i class="fa-solid fa-rotate-right"></i>
    <i class="fa-solid fa-house"></i>
  </div>

  <div class="searchbox">
    <script async src="https://cse.google.com/cse.js?cx=02cefcd74f6144687"></script>
    <div class="gcse-search"></div>
  </div>
</body>
</html>
