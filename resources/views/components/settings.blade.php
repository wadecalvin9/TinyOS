<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="{{ asset('./css/settings.css') }}">
    <script defer src="settings.js"></script>
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="settings-container">
        <div class="sidebar">
            <div class="sidebar-title">Settings</div>

            <div class="menu">
                <a href="/settings" class="menu-item {{ request()->is('settings') ? 'active' : '' }}"><i
                        class="fa-solid fa-desktop"></i> System</a>
                <a href="/settings/devices" class="menu-item {{ request()->is('settings/devices') ? 'active' : '' }}"><i
                        class="fa-solid fa-keyboard"></i> Devices</a>
                <a href="/settings/network" class="menu-item {{ request()->is('settings/network') ? 'active' : '' }}"><i
                        class="fa-solid fa-wifi"></i> Network</a>
                <a href="/settings/personalise"
                    class="menu-item {{ request()->is('settings/personalise') ? 'active' : '' }}"><i
                        class="fa-solid fa-palette"></i> Personalization</a>
                <a href="/settings/privacy" class="menu-item {{ request()->is('settings/privacy') ? 'active' : '' }}"><i
                        class="fa-solid fa-lock"></i> Privacy</a>
                <a href="/settings/about" class="menu-item {{ request()->is('settings/about') ? 'active' : '' }}"><i
                        class="fa-solid fa-info-circle"></i> About</a>
            </div>
        </div>
        <!-- Main Content -->
        <main class="content">
            {{ $slot }}
        </main>
    </div>


</body>

</html>
