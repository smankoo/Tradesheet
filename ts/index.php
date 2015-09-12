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
    </script>

    <script type="text/javascript">
        var row_num = 1;

        function addRow() {
            row_num++;
            var table = document.getElementById("stocks_table");
            var row = table.insertRow(document.getElementById("stocks_table").rows.length);
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

            cell1.innerHTML = "<input class=\"form-control\" id=\"symbol" + row_num + "\" type=\"text\" onblur=\"populateStockInfo(" + row_num + ", document.getElementById(&quot;symbol" + row_num + "&quot;).value);\">";
            cell2.innerHTML = "<input class=\"form-control\" id=\"isinNumber" + row_num + "\" type=\"text\">";
            cell3.innerHTML = "<input class=\"form-control\" id=\"securityName" + row_num + "\" type=\"text\">";
            cell4.innerHTML = "<select class=\"combobox\" id=\"country" + row_num + "\"> <option value=\"\"></option> <option value=\"usa\">USA</option> <option value=\"canada\">Canada</option> </select>";
            cell5.innerHTML = "<select class=\"combobox\" id=\"side" + row_num + "\" onchange=\"updateMaxShares(" + row_num + ");\"> <option value=\"\"></option> <option value=\"buy\">Buy</option> <option value=\"sell\">Sell</option> </select> ";
            cell6.innerHTML = "<input class=\"form-control\" id=\"shares" + row_num + "\" type=\"number\" min=\"0\" onblur=\"validateShareCount(" + row_num + ");\" onchange=\"updateRowTotal(" + row_num + ");\">";
            cell7.innerHTML = "<input class=\"form-control\" id=\"maxShares" + row_num + "\" value=\"\" onchange=\"validateShareCount(" + row_num + ");\" disabled>";
            cell8.innerHTML = "<select class=\"combobox\" id=\"orderType" + row_num + "\"> <option value=\"\"></option> <option value=\"day\">Day</option> <option value=\"gtc\">GTC</option> </select>";
            cell9.innerHTML = "<input class=\"form-control\" id=\"limitPrice" + row_num + "\" type=\"number\" min=\"0\" value=\"0\" onchange=\"updateRowTotal(" + row_num + ");\">";
            cell10.innerHTML = "<input class=\"form-control\ text-right\" id=\"total" + row_num + "\" type=\"text\" disabled>";

        }

        function updateRowTotal(rowid) {
            var sharesCell = document.getElementById('shares' + rowid);
            var limitpriceCell = document.getElementById('limitPrice' + rowid);
            var totalValue = sharesCell.value * limitpriceCell.value;
            var totalCell = document.getElementById('total' + rowid);
            totalCell.value = totalValue;
            updateSheetTotal();
        }

        function updateMaxShares(rowid) {
            var sideCell = document.getElementById("side" + rowid);
            var maxSharesCell = document.getElementById("maxShares" + rowid);

            if (sideCell.value == "buy") {
                maxSharesCell.value = "N/A";
            } else if (sideCell.value == "sell") {
                populateMaxShares(rowid);
            } else {
                maxSharesCell.value = "";
            }
            updateSheetTotal();
        }

        function populateStockInfo(rowid, str) {
            if (str == "") {
                document.getElementById("securityName" + rowid).value = "";
                document.getElementById("isinNumber" + rowid).value = "";
                document.getElementById("country" + rowid).value = "";
                return;
            } else {
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        var responseJson = JSON.parse(xmlhttp.responseText);

                        document.getElementById("securityName" + rowid).value = responseJson.description;
                        document.getElementById("securityName" + rowid).disabled = true;
                        document.getElementById("isinNumber" + rowid).value = responseJson.isin;
                        document.getElementById("isinNumber" + rowid).disabled = true;

                        document.getElementById("country" + rowid).value = responseJson.country.toLowerCase();
                        document.getElementById("country" + rowid).disabled = true;

                        if (document.getElementById("side" + rowid).value == "sell" && (document.getElementById("maxShares" + rowid).value == "" || document.getElementById("maxShares" + rowid).value == "0")) {
                            populateMaxShares(rowid);
                        }
                    }
                }

                xmlhttp.open("GET", "getSecurityInfo.php?sym=" + str, true);
                xmlhttp.send();
            }
        }

        function populateMaxShares(rowid) {
            var str = document.getElementById("isinNumber" + rowid).value;
            if (str == "") {
                document.getElementById("maxShares" + rowid).value = "0";
                return;
            } else {
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function () {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                            var responseJson = JSON.parse(xmlhttp.responseText);
                            document.getElementById("maxShares" + rowid).value = responseJson.shares_par;
                            document.getElementById("shares" + rowid).max = responseJson.shares_par;
                        }
                    }
                    //alert("Sending : getSecurityInfo.php?sym=" + str);
                xmlhttp.open("GET", "getPortInfo.php?isin=" + str, true);
                xmlhttp.send();
            }
        }

        function validateShareCount(rowid) {
            var maxShares = parseFloat(document.getElementById("maxShares" + rowid).value, 10);
            var shareCount = parseFloat(document.getElementById("shares" + rowid).value, 10);
            if (!shareCount) {
                return;
            }
            if (shareCount > maxShares) {
                alert("Number of shares is higher than the number of shares you own");
                document.getElementById("shares" + rowid).focus();
                document.getElementById("shares" + rowid).select();
            }
        }

        function updateSheetTotal() {
            var oInputs = new Array();
            oInputs = document.getElementsByTagName( 'input' ) // store collection of all <input/> elements
            var sheetTotalPurchaseCAD = 0
            var sheetTotalPurchaseUSD = 0
            var sheetTotalSaleCAD = 0
            var sheetTotalSaleUSD = 0
            var calc_row_num;
            
            for ( i = 0; i < oInputs.length; i++ )
            { 
                // loop through and find <input type="text"/>
                if ( oInputs[i].type == 'text' && oInputs[i].id.substring(0,5) == 'total' )
                {
                    calc_row_num = oInputs[i].id.substring(5,6);
                    
                    if ( document.getElementById("side" + calc_row_num).value == "buy" ){
                        if ( document.getElementById("country" + calc_row_num).value == "canada" ){
                            sheetTotalPurchaseCAD += parseFloat(oInputs[i].value,10);
                        } else if ( document.getElementById("country" + calc_row_num).value == "usa" ){
                            sheetTotalPurchaseUSD += parseFloat(oInputs[i].value,10);
                        }
                    } else if ( document.getElementById("side" + calc_row_num).value == "sell" ){
                        if ( document.getElementById("country" + calc_row_num).value == "canada" ){
                            sheetTotalSaleCAD += parseFloat(oInputs[i].value,10);
                        } else if ( document.getElementById("country" + calc_row_num).value == "usa" ){
                            sheetTotalSaleUSD += parseFloat(oInputs[i].value,10);
                        }
                    }
                }
            }
            document.getElementById("sheetPurchaseTotalCAD").value = sheetTotalPurchaseCAD;
            document.getElementById("sheetPurchaseTotalUSD").value = sheetTotalPurchaseUSD;
            document.getElementById("sheetSaleTotalCAD").value = sheetTotalSaleCAD;
            document.getElementById("sheetSaleTotalUSD").value = sheetTotalSaleUSD;
            
            if ( sheetTotalPurchaseCAD > parseFloat(document.getElementById("cashAtHandCAD").value,"10") ){
                document.getElementById("sheetPurchaseTotalCAD").style.backgroundColor = "red";
                document.getElementById("sheetPurchaseTotalCAD").style.color = "white";
            } else {
                document.getElementById("sheetPurchaseTotalCAD").style.backgroundColor = "";
                document.getElementById("sheetPurchaseTotalCAD").style.color = "";
            }
            if ( sheetTotalPurchaseUSD > parseFloat(document.getElementById("cashAtHandUSD").value,"10") ){
                document.getElementById("sheetPurchaseTotalUSD").style.backgroundColor = "red";
                document.getElementById("sheetPurchaseTotalUSD").style.color = "white";
            } else {
                document.getElementById("sheetPurchaseTotalUSD").style.backgroundColor = "";
                document.getElementById("sheetPurchaseTotalUSD").style.color = "";
            }
            
        }
    </script>


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

        total-cell {
            width: 150px;
        }
    </style>



    <!--[if IE]>
        <script src="https://cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
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
                    <li><a href="#students" data-toggle="tab">Students</a></li>
                    <li><a href="#stocks" data-toggle="tab" onclick="loadStocks();">Stocks</a></li>
                    <li><a href="#portfolio" data-toggle="tab">Portfolio</a></li>
                </ul>
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
                                <h3>Tradesheet</h3></div>
                            <div class="panel-body">
                                <p class="text-right">
                                    <button type="button" class="btn btn-default" onclick="addRow();">Add Row</button>
                                    <button type="button" class="btn btn-default" onclick="test(1);">|| Test ||</button>
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
                                            <th style="width:100px;">LIMITPRICE</th>
                                            <th style="width:150px;">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input class="form-control" id="symbol1" type="text" onblur="populateStockInfo(1, document.getElementById(&quot;symbol1&quot;).value);">
                                            </td>
                                            <td>
                                                <input class="form-control" id="isinNumber1" type="text">
                                            </td>
                                            <td>
                                                <input class="form-control" id="securityName1" type="text">
                                            </td>
                                            <td style="vertical-align: text-bottom;">
                                                <select id="country1" onchange="updateSheetTotal();">
                                                    <option value=""></option>
                                                    <option value="usa">USA</option>
                                                    <option value="canada">Canada</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="side1" onchange="updateMaxShares(1);">
                                                    <option value=""></option>
                                                    <option value="buy">Buy</option>
                                                    <option value="sell">Sell</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="form-control" id="shares1" type="number" min="0" onblur="validateShareCount(1);" onchange="updateRowTotal(1);">
                                            </td>
                                            <td>
                                                <input class="form-control" id="maxShares1" value="" onchange="validateShareCount(1);" disabled>
                                            </td>
                                            <td>
                                                <select id="orderType1">
                                                    <option value=""></option>
                                                    <option value="day">Day</option>
                                                    <option value="gtc">GTC</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="form-control" id="limitPrice1" type="number" min="0" value="0" onchange="updateRowTotal(1);">
                                            </td>
                                            <td>
                                                <input class="form-control text-right" id="total1" type="text" disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div style="max-width: 450px; float: right;">
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
                                                    <input class="form-control text-right" id="cashAtHandCAD" type="text" value="<?php getCashOnHand("CAD"); ?>" disabled>
                                                </td>
                                                <td>
                                                    <input class="form-control text-right" id="sheetPurchaseTotalCAD" type="text" value="0" disabled>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>USD</td>
                                                <td>
                                                    <input class="form-control text-right" id="cashAtHandUSD" type="text" value="<?php getCashOnHand("USD"); ?>" disabled>
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
                                </div>
                            </div>
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
                                <div class="text-right">
                                    <a href="upload_portfolio.php" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Upload New Portfolio</a>

                                    <!-- Modal HTML -->
                                    <div id="myModal" class="modal fade">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <!-- Content will be loaded here from "remote.php" file -->
                                            </div>
                                        </div>
                                    </div>
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