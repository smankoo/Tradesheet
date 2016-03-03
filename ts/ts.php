<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tradesheet Project (Alpha)</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

    <style>
        .loadinggif {
            background: url('custom/ajax-loader.gif') no-repeat right center;
        }
    </style>

    <script type="text/javascript">
        // Javascript to enable link to tab
        var hash = document.location.hash;
        var prefix = "tab_";
        if (hash) {
            $('.nav-tabs a[href=' + hash.replace(prefix, "") + ']').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('shown', function (e) {
            window.location.hash = e.target.hash.replace("#", "#" + prefix);
        });
    </script>

    <script type="text/javascript">
        function loadStocks() {
            if (document.getElementById('stocks_frame').src != location.href + "stocks_table.php") {
                document.getElementById('stocks_frame').src = "stocks_table.php";
            }
        }
        
        //The function prepareSheet needs to live in this php file since it calls a SESSION variable
        function prepareSheet() {

            clearPrepTableBody();
            var prep_row_num = 1;
            var row_count = document.getElementById("stocks_table").rows.length - 1;

            while (prep_row_num < row_count) {
                if (document.getElementById("total" + prep_row_num).value != 0) {
                    var table = document.getElementById("preparedTableTbody");
                    var row = table.insertRow(table.rows.length);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    var cell5 = row.insertCell(4);
                    var cell6 = row.insertCell(5);
                    var cell7 = row.insertCell(6);
                    var cell8 = row.insertCell(7);
                    var cell9 = row.insertCell(8);
                    var cell10 = row.insertCell(9);
                    var cell11 = row.insertCell(10);

                    cell1.innerHTML = document.getElementById("symbol" + prep_row_num).value;
                    cell2.innerHTML = document.getElementById("isinNumber" + prep_row_num).value;
                    cell3.innerHTML = document.getElementById("securityName" + prep_row_num).value;

                    var e = document.getElementById("country" + prep_row_num);
                    var strE = e.options[e.selectedIndex].text;

                    cell4.innerHTML = strE;

                    e = document.getElementById("side" + prep_row_num);
                    strE = e.options[e.selectedIndex].text;

                    cell5.innerHTML = strE;
                    cell6.innerHTML = document.getElementById("shares" + prep_row_num).value;

                    e = document.getElementById("orderType" + prep_row_num);
                    strE = e.options[e.selectedIndex].text;

                    cell7.innerHTML = strE;

                    e = document.getElementById("mkt_or_limit" + prep_row_num);
                    strE = e.options[e.selectedIndex].text;

                    cell8.innerHTML = strE;
                    cell9.innerHTML = document.getElementById("limitPrice" + prep_row_num).value;
                    cell10.innerHTML = document.getElementById("total" + prep_row_num).value;
                    cell11.innerHTML = "<?php echo $_SESSION['trading_group']; ?>";
                }
                prep_row_num = prep_row_num + 1;
            }

            document.getElementById("preparedSheetDiv").style.display = 'block';
            prepareCurrencyConvTable();
            prepareSalePurchaseTables();

        }
    </script>

    <script src="ts.js"></script>

    <?php
    include 'getCashAmount.php';
    ?>

        <style>
            body {
                padding-top: 50px;
            }
            
            .starter-template {
                padding: 40px 15px;
                text-align: center;
            }
        </style>

        <!--[if IE]>
        <script src="https://cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body onload="pageloadCheckPortfolio();">

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container col-lg-12">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Tradesheet Tool (Alpha)</a>
            </div>

            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#tradesheet" data-toggle="tab">Home</a></li>
                    <li><a href="#portfolio" data-toggle="tab">Portfolio</a></li>
                    <li><a href="#stocks" data-toggle="tab" onclick="loadStocks();">Stocks</a></li>
                    <?php
                        if ($_SESSION['admin'] == 1){
                            echo "<li><a href=\"#students\" data-toggle=\"tab\">Students</a></li>";
                        }
                    ?>

                </ul>
                <div class="navbar-header pull-right">
                    <p class="navbar-text">
                        <?php
                            if ($_SESSION['admin'] == 1){
                                echo "admin";
                            }
                        ?>
                            <b><?php echo $_SESSION['user_email']; ?></b>
                            <?php echo $_SESSION['trading_group']; ?>
                    </p>

                    <ul class="nav navbar-nav">
                        <li><a href="mailto:sumeet.mankoo@mail.mcgill.ca?subject=Tradesheet%20Support">Contact Support</a></li>
                        <li><a href="?logout">Logout</a></li>
                    </ul>
                </div>
            </div>

            <!--.nav-collapse -->
        </div>
    </nav>

    <div class="container col-lg-12">
        <div class="starter-template">
            <div class="">
                <div class="panel panel-default">

                    <div id="my-tab-content" class="tab-content">

                        <div class="tab-pane active" id="tradesheet">
                            <div class="panel-heading">
                                <h1>Tradesheet</h1>
                                <h3>Trading Group : <?php echo $_SESSION['trading_group']; ?></h3>
                            </div>
                            <div class="panel-body">
                                <?php
                                    if ($_SESSION['admin'] == 1){
                                        ?>
                                    <textarea class="form-control" id="debugBox" cols="40" rows="5" placeholder="Debug Output Here"></textarea>
                                    <?php
                                    }
                                ?>
                                        <div id="upload_portfolio_div" style="display: none;">
                                            <div style="display: block; padding: 10px; margin: auto; background-color: #FF704D; border-radius: 10px;">Current Portfolio not found</div>
                                            <div style="padding: 10px;">
                                                <!-- <a href="upload_portfolio2.php" class="btn btn-primary" data-toggle="modal" data-target="#myModal2" id="uploadPortfolioBtn">Upload New Portfolio</a>-->
                                            </div>
                                            <div id="myModal2" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <!-- Content will be loaded here from "upload_portfolio2.php" file -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="text-right">
                                            <button type="button" class="btn btn-default" onclick="addRow();">Add Row</button>
                                        </p>
                                        <table id="stocks_table" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width:120px;">SYMBOL</th>
                                                    <th style="width:150px;">ISIN Number</th>
                                                    <th>NAME</th>
                                                    <th style="width:60px;">COUNTRY</th>
                                                    <th style="width:60px;">SIDE</th>
                                                    <th style="width:100px;">SHARES</th>
                                                    <th style="width:100px;">Max Shares</th>
                                                    <th style="width:110px;">ORDER TYPE</th>
                                                    <th style="width:110px;">MKT/LIMIT</th>
                                                    <th style="width:150px;">PRICE</th>
                                                    <th style="width:150px;">TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stocks_table_tbody">
                                                <tr>
                                                    <script>
                                                        addRow();
                                                    </script>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="max-width: 450px; margin: auto;">
                                            <table id="purchase_table" class="table table-striped table-bordered" style="width:300;">
                                                <thead>
                                                    <tr>
                                                        <th>Purchase Table</th>
                                                        <th style="width:170px;">Cash at Hand</th>
                                                        <th style="width:150px;">Sheet Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>CAD</td>
                                                        <td>
                                                            <input class="form-control text-right" id="cashAtHandCAD" type="text" value='<?php getCashOnHand("CAD"); ?>' disabled>
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right" id="sheetPurchaseTotalCAD" type="text" value="0" disabled>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>USD</td>
                                                        <td>
                                                            <input class="form-control text-right" id="cashAtHandUSD" type="text" value='<?php getCashOnHand("USD"); ?>' disabled>
                                                        </td>
                                                        <td>
                                                            <input class="form-control text-right" id="sheetPurchaseTotalUSD" type="text" value="0" disabled>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table id="sale_table" class="table table-striped table-bordered" style="width:300;">
                                                <thead>
                                                    <tr>
                                                        <th>Sale Table</th>
                                                        <th style="width:150px;">Sheet Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>CAD</td>
                                                        <td>
                                                            <input class="form-control text-right" id="sheetSaleTotalCAD" type="text" value="0" disabled>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>USD</td>
                                                        <td>
                                                            <input class="form-control text-right" id="sheetSaleTotalUSD" type="text" value="0" disabled>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>


                                            <button type="button" id="toggleCurrencyConvBtn" class="btn btn-default" onclick="toggleCurrencyConv();">Request Currency Conversion</button>

                                            <div id="currencyConvDiv" style="display: none; padding-top: 20px;">
                                                <table id="conv_table" class="table table-striped table-bordered" style="width:300;">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="3" style="text-align: center;">Currency Conversion Request</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="vertical-align: middle;">From</td>
                                                            <td>
                                                                <input class="form-control text-right" id="fromValue" type="number" value="1" onkeyup="updateToCurrValue();" onchange="updateToCurrValue();">
                                                            </td>
                                                            <td>
                                                                <input class="form-control text-left" id="fromCurrency" type="text" value="USD" disabled>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <span style="font-size: 11px;">Exchange Rate</span>
                                                            </td>
                                                            <td>
                                                                <input class="form-control text-right" id="exchangeRate" type="number" value="1" disabled>
                                                            </td>
                                                            <td>
                                                                <button type="button" id="switchCurrBtn" class="btn btn-default btn-block" onclick="switchCurr();"><span class="glyphicon glyphicon-resize-vertical"></span></button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="vertical-align: middle;">To</td>
                                                            <td>
                                                                <input class="form-control text-right" id="toValue" type="number" value="0" onkeyup="updateFromCurrValue();" onchange="updateFromCurrValue();">
                                                            </td>
                                                            <td>
                                                                <input class="form-control text-left" id="toCurrency" type="text" value="CAD" disabled>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <button type="button" class="btn btn-default" onclick="startOver();">Clear All</button>
                                            <button type="button" id="btnPrepareSheet" class="btn">Prepare Sheet</button>


                                        </div>
                                        <div style="clear: both;">



                                            <div id="preparedSheetDiv" style="display: none; padding-top: 20px;">
                                                <div id="email_body_div" style=" border-style: solid; border-width: 2px;;">
                                                    <table border="1" id="preparedTable" class="table table-striped table-bordered" style="margin: auto;">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:120px;">SYMBOL</th>
                                                                <th style="width:150px;">ISIN Number</th>
                                                                <th>NAME</th>
                                                                <th style="width:60px;">COUNTRY</th>
                                                                <th style="width:60px;">SIDE</th>
                                                                <th style="width:100px;">SHARES</th>
                                                                <th style="width:110px;">ORDER TYPE</th>
                                                                <th style="width:110px;">MKT/LIMIT</th>
                                                                <th style="width:100px;">PRICE</th>
                                                                <th style="width:150px;">TOTAL</th>
                                                                <th style="width:150px;">Account#</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="preparedTableTbody">
                                                        </tbody>

                                                    </table>
                                                    </br>

                                                    <div id="preppedCurrencyConvDiv" style="display: block; padding-top: 20px; max-width: 450px; margin: auto;">
                                                        <table border="1" id="prepped_conv_table" class="table table-striped table-bordered">
                                                            <thead id="prepped_conv_table_thead">
                                                            </thead>
                                                            <tbody id="prepped_conv_table_tbody">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    </br>
                                                    <div style="display: block; padding-top: 20px; max-width: 450px; margin: auto;">
                                                        <table border="1" id="purchase_table_prepped" class="table table-striped table-bordered" style="width:300;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Purchase Table</th>
                                                                    <th style="width:170px;">Cash at Hand</th>
                                                                    <th style="width:150px;">Sheet Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="purchase_table_prepped_tbody">
                                                            </tbody>
                                                        </table>
                                                        </br>

                                                        <table border="1" id="sale_table_prepped" class="table table-striped table-bordered" style="width:300;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sale Table</th>
                                                                    <th style="width:150px;">Sheet Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="sale_table_prepped_tbody">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <form>
                                                    <table id="email_table" class="table table-striped table-bordered" style="width:300;">
                                                        <tbody>
                                                            <tr>
                                                                <td>McGill ID:</td>
                                                                <td>
                                                                    <input class="form-control" id="userEmail" type="email" value="<?php print $_SESSION['user_email']; ?>">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Password:</td>
                                                                <td>
                                                                    <input class="form-control" id="userPass" type="password" placeholder="You McGill Email Password">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <input type="hidden" name="email_body_field" value="">



                                                    <button id="sendEmailBtn" type="button" class="btn btn-primary" onclick="sendEmail();">Send Email</button>
                                                    <button id="startOverBtn" type="button" class="btn btn-default" onclick="startOver();">Start Over</button>
                                                </form>

                                            </div>

                                        </div>
                            </div>
                            <!--.panel-body -->
                        </div>
                        <!--.tab-pane -->
                        <div class="tab-pane" id="stocks">
                            <div class="panel-heading">
                                <h3>Stocks</h3></div>
                            <div class="panel-body">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe id="stocks_frame" class="embed-responsive-item" src=""></iframe>
                                </div>
                            </div>
                        </div>
                        <!--.tab-pane -->
                        <div class="tab-pane" id="students">
                            <div class="panel-heading">
                                <h3>Students</h3></div>
                            <div class="panel-body">
                            </div>
                        </div>
                        <!--.tab-pane -->
                        <div class="tab-pane" id="portfolio">
                            <div class="panel-heading">
                                <h3>Portfolio</h3>
                            </div>
                            <div class="panel-body">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="portfolio_table.php"></iframe>
                                </div>
                            </div>
                        </div>
                        <!--.tab-pane -->

                    </div>


                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-combobox.js"></script>
</body>

</html>