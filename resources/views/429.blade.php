<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('stagefront.app_name') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,700">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            font-family: 'Lato', sans-serif;
            text-align: center;
            font-size: 20px;
        }
        section {
            padding: 1em;
        }
        small {
            font-size: .8em;
        }
        a, a:visited, a:active {
            text-decoration: none;
        }
        a.button {
            display: inline-block;
            width: 100%;
            max-width: 150px;
            line-height: 1.5em;
            padding: .5em;
            font-size: 1rem;
            box-shadow: none;
            background: #212121;
            color: #ffffff;
            border: 1px solid #212121;
            cursor: pointer;
            margin-top: 1em;
        }
        a.button:focus {
            outline: none;
            box-shadow: none;
            border: 1px solid #212121;
        }
        .caps {
            text-transform: uppercase;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>

<section>

    <h1 class="caps">{{ config('stagefront.app_name') }}</h1>

    <p>{{ trans('stagefront::errors.throttled.intro') }}</p>

    <p><small>{{ trans('stagefront::errors.throttled.remaining', ['remaining' => $timeRemaining]) }}</small></p>

    <p class="center">
        <a href="{{ config('stagefront.url') }}" class="button caps">
            &langle; {{ trans('stagefront::errors.throttled.back') }}
        </a>
    </p>

</section>

</body>
</html>
