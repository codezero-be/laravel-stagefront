<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
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
        .stagefront-form > div {
            display: flex;
            flex-direction: column;
            padding: .5em 1em;
        }
        .stagefront-form input, .stagefront-form button {
            flex-grow: 1;
        }
        .stagefront-live-link {
            font-size: .7em;
        }
        input, button {
            line-height: 1.5em;
            padding: .5em;
            font-size: 1rem;
            box-shadow: none;
        }
        input:focus, button:focus {
            outline: none;
            box-shadow: none;
            border: 1px solid #212121;
        }
        input {
            border: 1px solid #cccccc;
        }
        button {
            background: #212121;
            color: #ffffff;
            border: 1px solid #212121;
            cursor: pointer;
        }
        input::-webkit-input-placeholder {
            color: #cccccc;
        }
        input:-moz-placeholder {
            color: #cccccc;
            opacity: 1;
        }
        input::-moz-placeholder {
            color: #cccccc;
            opacity: 1;
        }
        input:-ms-input-placeholder {
            color: #cccccc;
        }
        a, a:visited, a:active {
            color: #00a7ed;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: #ea000d;
            margin: 1em 0 0;
            font-size: .7em;
        }
        .sr-only {
            display: none;
        }
        .caps {
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<section class="stagefront-screen">

    <h1 class="stagefront-title caps">{{ config('app.name') }}</h1>

    <p>{{ trans('stagefront::form.intro') }}</p>

    <form action="{{ config('stagefront.url') }}" method="post" class="stagefront-form">
        {{ csrf_field() }}

        <div>
            <label class="sr-only">
                {{ trans('stagefront::form.labels.login') }}
            </label>
            <input name="login"
                   value="{{ old('login') }}"
                   placeholder="{{ trans('stagefront::form.labels.login') }}"
                   autocomplete="off"
                   autofocus
                   required>
            {!! $errors->first('login', '<p class="error">&cross; :message</p>') !!}
        </div>

        <div>
            <label class="sr-only">
                {{ trans('stagefront::form.labels.password') }}
            </label>
            <input name="password"
                   type="password"
                   placeholder="{{ trans('stagefront::form.labels.password') }}"
                   required>
            {!! $errors->first('password', '<p class="error">&cross; :message</p>') !!}
        </div>

        <div>
            <button type="submit" class="caps">
                {{ trans('stagefront::form.buttons.submit') }} &rangle;
            </button>
        </div>

        @if ($liveSite)
            <div class="stagefront-live-link">
                {!! trans('stagefront::form.live', $liveSite) !!}
            </div>
        @endif

    </form>

</section>

</body>
</html>
