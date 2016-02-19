<?php
// show potential errors / feedback (from login object)
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <title>Tradesheet Login</title>
        <meta name="generator" content="Bootply" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
        <link href="css/styles.css" rel="stylesheet">
    </head>

    <body>
        <!--login modal-->
        <div id="loginModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                        <h1 class="text-center">Login</h1>
                    </div>
                    <div class="modal-body">
                        <form class="form col-md-12 center-block" method="post" action="index.php" name="loginform">
                            <div class="form-group">
                                <input type="text" class="form-control input-lg login_input" placeholder="Email" id="login_input_username" name="user_name">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control input-lg login_input" placeholder="Password" id="login_input_password" name="user_password">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary btn-lg btn-block" name="login" value="Sign In" />
                                <!-- <span class="pull-right"><a href="register.php">Register</a></span> -->

                                <span> <a href="mailto:sumeet.mankoo@mail.mcgill.ca?subject=Tradesheet%20Tech%20Support">Need help?</a></span>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <!-- <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- script references -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>

    </html>