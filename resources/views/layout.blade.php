<!DOCTYPE html>
<html>
<head>
    <title>NTTI Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Bayon&family=Moul&family=Noto+Serif+Khmer:wght@800&family=Suwannaphum&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="https://nttiportal.com/./uploads/school_content/logo/front_fav_icon-619eeffe4674b6.56720560.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Raleway:300,400,600);

        body {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 400;
            color: #212529;
            text-align: left;
            background-color: #202020; 
            background-image: url('asset/NTTI/images/img_login.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            height: auto;
        }

        /* Dark overlay effect */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(3, 3, 3, 0.578); /* Adjust opacity for darkness */
            z-index: 0;
        }
        .navbar-laravel
        {
            box-shadow: 0 2px 4px rgba(0,0,0,.04);
            background: #fbfbfb;
        }
        .navbar-brand , .nav-link, .my-form, .login-form
        {
            font-family: Raleway, sans-serif;
        }
        .my-form
        {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .my-form .row
        {
            margin-left: 0;
            margin-right: 0;
        }
        .login-form
        {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .login-form .row
        {
            margin-left: 0;
            margin-right: 0;
        }
        .khmer-mef2{
            font-size: 25px !important;
            color: #0f4e87 !important;
        }
        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #d0d0d0;
            outline: 0;
            box-shadow: none;
}
    </style>
</head>
<body>
    
<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#"><img src="asset/NTTI/images/logo.png" alt="logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
   
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                @guest
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li> --}}
                @else
                    <li class="nav-item">
                        {{-- <a class="nav-link" href="{{ route('logout') }}">Logout</a> --}}
                    </li>
                @endguest
            </ul>
  
        </div>
    </div>
</nav>
  
@yield('content')
     
</body>
</html>