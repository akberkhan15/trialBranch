<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="Creative Tim">
    <link rel="stylesheet" href="{{asset('css/app.css')}}" type="text/css" />
    <title>Seller Xperts</title>
</head>
<body>

<div id="app"></div>
<script>
           window.Laravel = <?php echo json_encode([
               'csrfToken' => csrf_token(),
                    ]); ?>
          </script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- <script src="{{asset('assets/vendor/jquery/dist/jquery.min.js')}}"></script> -->
<script src="./js/app.js"></script>
</body>
</html>