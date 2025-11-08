
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fullscreen Iframe</title>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      overflow: hidden; /* remove scrolling */
    }

    .video-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
    }

    .video-container iframe {
      width: 100%;
      height: 100%;
      border: none;
      display: block;
    }
  </style>
</head>

<body>
  <div class="video-container">
    <iframe src="https://www.retrogames.cc/embed/28096-dragon-ball-z-supersonic-warriors-k-projectg.html"
      frameborder="0"
      allowfullscreen
      scrolling="no"></iframe>
  </div>
</body>

</html>

