<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tradesheet Project</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">


    <style>
        body {
            padding-top: 50px;
        }
        
        .starter-template {
            padding: 40px 15px;
            text-align: center;
        }
        
        .btn-file {
            position: relative;
            overflow: hidden;
        }
        
        .btn-file input[type=file] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            background: white;
            cursor: inherit;
            display: block;
        }
    </style>

    <!--[if IE]>
        <script src="https://cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Tradesheet Tool</a>
            </div>

            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <!--.nav-collapse -->
        </div>
    </nav>

    <div class="container">
        <div class="starter-template">
            <h1>Tradesheet Project</h1>

            <div class="container" style="margin-top: 20px;">
                <div class="row">

                    <form action="processupload.php" method="post" enctype="multipart/form-data" id="MyUploadForm">
                        <input name="FileInput" id="FileInput" type="file" />
                        <input type="submit" id="submit-btn" value="Upload" />
                        <img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait" />
                    </form>
                    <div id="progressbox">
                        <div id="progressbar"></div>
                        <div id="statustxt">0%</div>
                    </div>
                    <div id="output"></div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <!--javascript for the ajax upload-->
    <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="js/jquery.form.min.js"></script>
    <script type="text/javascript" src="js/ajax-upload.js"></script>

</body>

</html>