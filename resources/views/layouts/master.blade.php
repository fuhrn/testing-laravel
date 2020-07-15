<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TicketBeast')</title>

{{--    stripe--}}
    <script src="https://js.stripe.com/v3/"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{--    stripe--}}
    <style>
        .spacer {
            margin-bottom: 24px;
        }
        /**
         * The CSS shown here will not be introduced in the Quickstart guide, but shows
         * how you can use CSS to style your Element's container.
         */
        .StripeElement {
            background-color: white;
            padding: 10px 12px;
            border-radius: 4px;
            border: 1px solid #ccd0d2;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }
        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }
        .StripeElement--invalid {
            border-color: #fa755a;
        }
        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
        #card-errors {
            color: #fa755a;
        }
    </style>

</head>
<body>
<div id="app">
    @yield('body')
</div>

{{--@stack('beforeScripts')--}}
{{--<script src="{{ elixir('js/app.js') }}"></script>--}}
{{--@stack('afterScripts')--}}
{{--{{ svg_spritesheet() }}--}}
</body>
</html>
