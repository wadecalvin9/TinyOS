<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GlassOS UI</title>
    <link rel="stylesheet" href="{{ asset('./css/index.css') }}" />
    <script defer src="{{ asset('./js/index.js') }}"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- jQuery + UI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
</head>

<body>
    <!-- Time + Date -->
    <div class="time-container">
        <p id="time">13:13</p>
        <div><i class="fa-solid fa-moon"></i> 21°C</div>
        <p id="date">Thursday, March 2025</p>
    </div>
    <div class="maincontainer" style="background-image: url('{{ $settings->background_url }}');">


        <!-- Start Menu -->
        <div class="window" id="startMenu">
            <x-search />
            <div class="windowicons">
                <i id="settingsbtn" class="fa-solid fa-gear app" title="Settings" data-url="apps/settings.html"></i>
                <i id="browserbtn" class="fa-solid fa-globe app" title="Web Browser" data-url="apps/browser.html"></i>
                <i id="youtubebtn" class="fa-brands fa-youtube app" title="Youtube"
                    data-url="{{ route('youtube') }}"></i>
                <i id="Dbzbtn" title="dbz" data-url="/dbz" class="app"><img class="iconimage "
                        src="https://i.pinimg.com/736x/5a/8e/04/5a8e04a3eab0ce631d33a6631244c354.jpg" alt="">

                </i>

            </div>

            <div class="account">
                <div>
                    @guest
                        <a href="/login"><i class="fa-regular fa-user"></i></a>
                    @endguest


                    @auth
                        <div class="avatar">
                            <img src="https://images.pexels.com/photos/3109944/pexels-photo-3109944.jpeg" alt="">
                        </div>
                        <div class="username">
                            <p>{{ auth()->user()->name }}</p>

                        </div>
                    @endauth
                </div>
                <div class="logout">
                    <i id="settingsbtn" class="fa-solid fa-gear app" title="Settings"
                        data-url="{{ route('settings.index') }}"></i>
                    @auth
                        <form action="{{ route('auth.logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="navlink" style="background: none; border: none; cursor: pointer;">
                                <i class="fa-solid fa-power-off"></i>
                            </button>
                        </form>
                    @endauth



                </div>


            </div>
        </div>

        <!-- Taskbar -->
        <div class="taskbar">
            <div class="icons">
                <i id="windowbtn" class="fa-brands fa-windows"></i>
                @auth
                                    <i id="app1btn" class="fa-solid fa-folder app" title="File Explorer" data-url="{{ route('explorer') }}"
                    style="color:#FFD43B;"></i>

                @endauth
                <i id="settingsbtn" class="fa-solid fa-gear app" title="Settings"
                    data-url="{{ route('settings.index') }}"></i>
                <i id="browserbtn" class="fa-solid fa-globe app" title="Web Browser"
                    data-url="{{ route('browser') }}"></i>
            </div>
        </div>
    </div>
</body>

</html>
