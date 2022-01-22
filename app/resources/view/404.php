<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>404</title>
        <style>
        body {
          font-family: monospace, system-ui;
          background: #151515;
          color: #b2b2b2;
          padding: 1rem;
        }
        h1 {
            margin: 0;
            color: #fff;
        }
        h2 {
            margin: 0;
            color: #777;
        }
        </style>
    </head>
    <body>
        <h1>404</h1>
        <h2><?php echo (isset($message) !== false ? $message : 'Page not found!'); ?></h2>
    </body>
</html>