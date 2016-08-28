<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <meta content="Jan Komadowski" name="copyright"/>
    <meta content="Jan Komadowski" name="author"/>
    <meta name="description"
          content="TCX Composer allows you to merge, crop, split or concat two or more workout files">

    <link rel="stylesheet" href="{{ url('/css/main.css') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    @yield('head')
    @yield('page-title')
</head>
<body>
<div class="container">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ url('/') }}">TCX Composer</a>
            </div>
            <div id="navbar">
                <ul class="nav navbar-nav">
                    <li @if(Request::is('news'))class="active"@endif><a href="{{ url('/news') }}">News</a></li>
                    <li @if(Request::is('workouts'))class="active"@endif><a href="{{ url('/workouts') }}">Workouts</a></li>
                    <li @if(Request::is('merge'))class="active"@endif><a href="{{ url('/merge') }}">Merge</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false">Profile <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url('/profile') }}">View Profile</a></li>
                            <li><a href="{{ url('/settings') }}">Settings</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ url('/auth/login') }}">Login</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header>
    </header>
    <section class="page-content">
        @yield('page-content')
    </section>
    <footer class="footer">
        <p>Copyright © 2016 Workout Merge</p>
    </footer>
</div>
</body>
</html>
