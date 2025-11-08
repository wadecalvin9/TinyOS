$(document).ready(function () {
  // ---------------- CLOCK ----------------
  function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    const date = now.toLocaleDateString([], { weekday: "long", month: "long", day: "numeric" });
    $("#time").text(time);
    $("#date").text(date);
  }
  setInterval(updateClock, 1000);
  updateClock();

  // ---------------- START MENU ----------------
  const startMenu = $("#startMenu");
  $("#windowbtn").on("click", function (e) {
    e.stopPropagation();
    startMenu.toggleClass("active");
  });
  $(document).on("click", function (e) {
    if (!$(e.target).closest("#startMenu, #windowbtn").length) {
      startMenu.removeClass("active");
    }
  });

  // ---------------- GLOBAL Z-INDEX HANDLER ----------------
  let topZ = 100;

  function bringToFront(win) {
    topZ++;
    $(".appwindows").removeClass("focused");
    win.css("z-index", topZ).addClass("focused");
  }

  // ---------------- OPEN APP WINDOW ----------------
  $(".app").on("click", function () {
    const appId = $(this).attr("id");
    const url = $(this).data("url");
    const title = $(this).attr("title") || "App";
    const taskBtn = $(this);

    // Check if window exists
    let win = $(`#window-${appId}`);
    if (win.length) {
      if (win.hasClass("minimized")) {
        win.removeClass("minimized").fadeIn(200).addClass("active");
        taskBtn.addClass("active");
      } else {
        // Toggle minimize when clicking same icon
        win.addClass("minimized").fadeOut(200);
        taskBtn.removeClass("active");
      }
      bringToFront(win);
      return;
    }

    // Create new window
    win = $(`
      <div class="appwindows active" id="window-${appId}" data-appbtn="#${appId}">
        <div class="window-header">
          <span class="window-title">${title}</span>
          <div class="window-controls">
            <i class="fa-solid fa-minus minimize" title="Minimize"></i>
            <i class="fa-regular fa-square maximize" title="Maximize"></i>
            <i class="fa-solid fa-xmark close" title="Close"></i>
          </div>
        </div>
        <div class="window-body">
          ${
            url
              ? `<iframe src="${url}" frameborder="0" class="app-frame"></iframe>`
              : `<p>App content for <strong>${title}</strong></p>`
          }
        </div>
      </div>
    `).appendTo(".maincontainer");

    // Make draggable and resizable
    win.draggable({
      handle: ".window-header",
      containment: "window",
      start: function () {
        $(this).addClass("dragging");
        bringToFront($(this));
      },
      stop: function () {
        $(this).removeClass("dragging");
      }
    }).resizable({
      minWidth: 300,
      minHeight: 250,
      start: function () {
        $(this).addClass("resizing");
        bringToFront($(this));
      },
      stop: function () {
        $(this).removeClass("resizing");
      }
    });

    bringToFront(win);
    taskBtn.addClass("active");
  });

  // ---------------- WINDOW CONTROLS ----------------
  $(document).on("click", ".window-controls .close", function () {
    const win = $(this).closest(".appwindows");
    const btn = $(win.data("appbtn"));
    win.fadeOut(200, function () {
      win.remove();
    });
    btn.removeClass("active");
  });

  $(document).on("click", ".window-controls .minimize", function () {
    const win = $(this).closest(".appwindows");
    const btn = $(win.data("appbtn"));
    win.addClass("minimized").fadeOut(200);
    btn.removeClass("active");
  });

  $(document).on("click", ".window-controls .maximize", function () {
    const win = $(this).closest(".appwindows");
    win.toggleClass("maximized");
  });

  // ---------------- FOCUS HANDLING ----------------
  $(document).on("mousedown", ".appwindows", function () {
    bringToFront($(this));
  });

  // Double click title bar to toggle maximize
  $(document).on("dblclick", ".window-header", function () {
    $(this).closest(".appwindows").toggleClass("maximized");
  });
});
