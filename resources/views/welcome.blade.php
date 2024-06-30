<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

        <!-- Style -->
        <style>
            html, body {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
                padding: 0;
                margin: 0;
                background-color: #f1f5f9;
            }

            h1 {
                font-weight: 100;
                font-family: 'Roboto';
                color: #374151;
                font-size: 3rem;
                letter-spacing: 0.25rem;
                margin-bottom: 0;
            }

            p {
                font-family: 'Roboto';
                font-weight: 300;
                color: #6b7280;
                font-size: 1.1rem;
            }
        </style>
    </head>

    <body>
        <div style="text-align: center">
            <h1>Sentinel Agent <span style="font-weight: 300;">[{{ config('app.location') }}]</span></h1>
            <p>
                The external agent that processes various functions for the main application.
            </p>
        </div>
    </body>
</html>
