<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('./css/auth.css') }}">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="maincontainer">
        <div class="authdetails">
            <div class="avatar">
                <img src="https://images.pexels.com/photos/3109944/pexels-photo-3109944.jpeg" alt="">


            </div>



            {{ $slot }}

        </div>


    </div>

</body>

</html>
