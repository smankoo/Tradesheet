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
        function addRow() {
            var table = document.getElementById("stocks_table_tbody");

            row_num = table.rows.length + 1;
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

            cell1.innerHTML = "<input class=\"form-control\" id=\"symbol" + row_num + "\" type=\"text\" onkeyup=\"javascript:capitalize(this.id, this.value);\" onblur=\"getStockInfoYahoo(" + row_num + ");\">";
            cell2.innerHTML = "<input class=\"form-control\" id=\"isinNumber" + row_num + "\" type=\"text\">";
            cell3.innerHTML = "<input class=\"form-control\" id=\"securityName" + row_num + "\" type=\"text\">";
            cell4.innerHTML = "<select class=\"combobox\" id=\"country" + row_num + "\"> <option value=\"\"></option> <option value=\"usa\">USA</option> <option value=\"canada\">Canada</option> </select>";
            cell5.innerHTML = "<select class=\"combobox\" id=\"side" + row_num + "\" onchange=\"updateMaxShares(" + row_num + ");\"> <option value=\"\"></option> <option value=\"buy\">Buy</option> <option value=\"sell\">Sell</option> </select> ";
            cell6.innerHTML = "<input class=\"form-control\" id=\"shares" + row_num + "\" type=\"number\" value=\"0\" min=\"0\" onblur=\"validateShareCount(" + row_num + ");\" onkeyup=\"updateRowTotal(" + row_num + ");\" onchange=\"updateRowTotal(" + row_num + ");\" disabled>";
            cell7.innerHTML = "<input class=\"form-control\" id=\"maxShares" + row_num + "\" value=\"0\" onchange=\"validateShareCount(" + row_num + ");\" disabled>";
            cell8.innerHTML = "<select class=\"combobox\" id=\"orderType" + row_num + "\">  <option value=\"gtc\">GTC</option> <option value=\"day\">Day</option></select>";
            cell9.innerHTML = "<td><select id=\"mkt_or_limit" + row_num + "\" onchange=\"mktLimitChanged(" + row_num + ");\"><option value=\"mkt\">MKT</option><option value=\"limit\">Limit</option></select></td>";
            cell10.innerHTML = "<input class=\"form-control\" id=\"limitPrice" + row_num + "\" type=\"number\" min=\"0\" value=\"0\" onchange=\"updateRowTotal(" + row_num + ");\" disabled>";
            cell11.innerHTML = "<input class=\"form-control text-right\" id=\"total" + row_num + "\" type=\"number\" value=\"0\" disabled>";

        }

        function updateRowTotal(rowid) {
            var sharesCell = document.getElementById('shares' + rowid);
            var limitpriceCell = document.getElementById('limitPrice' + rowid);
            var totalValue = Math.round(sharesCell.value * limitpriceCell.value * 100) / 100;
            var totalCell = document.getElementById('total' + rowid);
            totalCell.value = totalValue;
            updateSheetTotal();
        }

        function updateMaxShares(rowid) {
            var sideCell = document.getElementById("side" + rowid);
            var maxSharesCell = document.getElementById("maxShares" + rowid);

            if (sideCell.value == "buy") {
                maxSharesCell.value = "N/A";
                document.getElementById("shares" + rowid).max = "";
                document.getElementById("shares" + rowid).disabled = false;
            } else if (sideCell.value == "sell") {
                document.getElementById("shares" + rowid).value = "0";
                document.getElementById("shares" + rowid).disabled = false;
                populateMaxShares(rowid);
            } else {
                document.getElementById("shares" + rowid).disabled = true;
                maxSharesCell.value = "";
            }
            updateSheetTotal();
        }

        function populateStockInfoFromDB(rowid) {
            var str = document.getElementById("symbol" + rowid).value;

            if (str == "") {
                document.getElementById("isinNumber" + rowid).value = "";
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

                        if (xmlhttp.responseText != "NOTFOUND") {

                            var responseJson = JSON.parse(xmlhttp.responseText);

                            //document.getElementById("securityName" + rowid).value = responseJson.description;
                            //document.getElementById("securityName" + rowid).disabled = true;
                            document.getElementById("isinNumber" + rowid).value = responseJson.isin;
                            document.getElementById("isinNumber" + rowid).disabled = true;

                            //document.getElementById("country" + rowid).value = responseJson.country.toLowerCase();
                            //document.getElementById("country" + rowid).disabled = true;

                            if (document.getElementById("side" + rowid).value == "sell" && (document.getElementById("maxShares" + rowid).value == "" || document.getElementById("maxShares" + rowid).value == "0")) {
                                populateMaxShares(rowid);
                            }

                        } else {
                            document.getElementById("isinNumber" + rowid).value = "";
                            document.getElementById("isinNumber" + rowid).disabled = false;

                            document.getElementById("maxShares" + rowid).value = "0";
                            document.getElementById("shares" + rowid).max = "0";
                        }

                    }
                };
                //alert("getSecurityInfo.php?sym=" + str);
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
                        if (xmlhttp.responseText != "NOTFOUND") {
                            var responseJson = JSON.parse(xmlhttp.responseText);
                            document.getElementById("maxShares" + rowid).value = responseJson.shares_par;
                            document.getElementById("shares" + rowid).max = responseJson.shares_par;
                        } else {
                            document.getElementById("maxShares" + rowid).value = "0";
                            document.getElementById("shares" + rowid).max = "0";
                        }
                    }
                };
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
            oInputs = document.getElementsByTagName('input'); // store collection of all <input/> elements
            var sheetTotalPurchaseCAD = 0;
            var sheetTotalPurchaseUSD = 0;
            var sheetTotalSaleCAD = 0;
            var sheetTotalSaleUSD = 0;
            var calc_row_num;

            for (i = 0; i < oInputs.length; i++) {
                // loop through and find <input type="text"/>
                if (oInputs[i].type == 'number' && oInputs[i].id.substring(0, 5) == 'total') {
                    calc_row_num = oInputs[i].id.substring(5, 6);

                    if (document.getElementById("side" + calc_row_num).value == "buy") {
                        if (document.getElementById("country" + calc_row_num).value == "canada") {
                            sheetTotalPurchaseCAD += parseFloat(oInputs[i].value, 10);
                        } else if (document.getElementById("country" + calc_row_num).value == "usa") {
                            sheetTotalPurchaseUSD += parseFloat(oInputs[i].value, 10);
                        }
                    } else if (document.getElementById("side" + calc_row_num).value == "sell") {
                        if (document.getElementById("country" + calc_row_num).value == "canada") {
                            sheetTotalSaleCAD += parseFloat(oInputs[i].value, 10);
                        } else if (document.getElementById("country" + calc_row_num).value == "usa") {
                            sheetTotalSaleUSD += parseFloat(oInputs[i].value, 10);
                        }
                    }
                }
            }
            document.getElementById("sheetPurchaseTotalCAD").value = sheetTotalPurchaseCAD;
            document.getElementById("sheetPurchaseTotalUSD").value = sheetTotalPurchaseUSD;
            document.getElementById("sheetSaleTotalCAD").value = sheetTotalSaleCAD;
            document.getElementById("sheetSaleTotalUSD").value = sheetTotalSaleUSD;

            colorizeTotals();

        }

        function colorizeTotals() {

            var sheetTotalPurchaseCAD = document.getElementById("sheetPurchaseTotalCAD").value;
            var sheetTotalPurchaseUSD = document.getElementById("sheetPurchaseTotalUSD").value;

            var cashAtHandCAD = parseFloat(document.getElementById("cashAtHandCAD").value, "10");
            var cashAtHandUSD = parseFloat(document.getElementById("cashAtHandUSD").value, "10");

            if (CURRENCY_CONV_REQUESTED) {
                var toCurrency = document.getElementById("toCurrency").value;
                if (toCurrency == "USD") {
                    cashAtHandUSD = cashAtHandUSD + parseFloat(document.getElementById("toValue").value, "10");
                } else {
                    cashAtHandCAD = cashAtHandCAD + parseFloat(document.getElementById("toValue").value, "10");
                }
            }

            if (sheetTotalPurchaseCAD > cashAtHandCAD) {
                document.getElementById("sheetPurchaseTotalCAD").style.backgroundColor = "red";
                document.getElementById("sheetPurchaseTotalCAD").style.color = "white";
                ERROR_STATUS_TOTAL_CAD = true;
            } else {
                document.getElementById("sheetPurchaseTotalCAD").style.backgroundColor = "";
                document.getElementById("sheetPurchaseTotalCAD").style.color = "";
                ERROR_STATUS_TOTAL_CAD = false;
            }
            if (sheetTotalPurchaseUSD > cashAtHandUSD) {
                document.getElementById("sheetPurchaseTotalUSD").style.backgroundColor = "red";
                document.getElementById("sheetPurchaseTotalUSD").style.color = "white";
                ERROR_STATUS_TOTAL_USD = true;
            } else {
                document.getElementById("sheetPurchaseTotalUSD").style.backgroundColor = "";
                document.getElementById("sheetPurchaseTotalUSD").style.color = "";
                ERROR_STATUS_TOTAL_USD = false;
            }
        }

        function setMaxFromConv() {

            var maxVal = 0;
            if (document.getElementById("fromCurrency").value == "USD") {
                maxVal = Math.round(parseFloat(document.getElementById("cashAtHandUSD").value, "10")) - 1;
            } else {
                maxVal = Math.round(parseFloat(document.getElementById("cashAtHandCAD").value, "10")) - 1;
            }
            document.getElementById("fromValue").max = maxVal;
        }

        function clearPrepTableBody() {
            var tbody = document.getElementById("preparedTableTbody");
            tbody.innerHTML = "";
        }

        function prepTableHead() {

            clearPrepTableBody();

            var table = document.getElementById("preparedTable");

            var header = table.createTHead();
            var row = header.insertRow(0);
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

            cell1.style = "width: 120px;";
            cell2.style = "width: 150px;";
            cell3.style = "";
            cell4.style = "width: 60px;";
            cell5.style = "width: 100px;";
            cell6.style = "width: 100px;";
            cell7.style = "width: 110px;";
            cell8.style = "width: 110px;";
            cell9.style = "width: 100px;";
            cell10.style = "width: 150px;";
            cell11.style = "width: 150px;";

            cell1.innerHTML = "SYMBOL";
            cell2.innerHTML = "ISIN Number";
            cell3.innerHTML = "NAME";
            cell4.innerHTML = "COUNTRY";
            cell5.innerHTML = "SIDE";
            cell6.innerHTML = "SHARES";
            cell7.innerHTML = "ORDER TYPE";
            cell8.innerHTML = "MKT/LIMIT";
            cell9.innerHTML = "LIMITPRICE";
            cell10.innerHTML = "TOTAL";
            cell11.innerHTML = "ACCOUNT";

        }

        function prepareSheet() {

            clearPrepTableBody();
            var prep_row_num = 0;
            var row_count = document.getElementById("stocks_table").rows.length - 1;

            while (prep_row_num < row_count) {
                prep_row_num++;
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

            }


            document.getElementById("preparedSheetDiv").style.display = 'block';
            prepareCurrencyConvTable();

        }

        CURRENCY_CONV_REQUESTED = false;

        function toggleCurrencyConv() {
            if (!CURRENCY_CONV_REQUESTED) {
                CURRENCY_CONV_REQUESTED = true;
                document.getElementById("currencyConvDiv").style.display = 'block';
                setMaxFromConv();
                getExchangeRate();
            } else {
                CURRENCY_CONV_REQUESTED = false;
                document.getElementById("currencyConvDiv").style.display = 'none';
            }
            colorizeTotals();
        }


        function startOver() {
            document.getElementById("preparedSheetDiv").style.display = 'none';
            document.getElementById("stocks_table_tbody").innerHTML = "";
            addRow();

        }

        function capitalize(textboxid, str) {
            var str = str.toUpperCase();
            document.getElementById(textboxid).value = str;
        }

        function getStockInfoYahoo(rowid) {
            var str = document.getElementById("symbol" + rowid).value;

            // Check for blankness
            if (str == "") {
                document.getElementById("limitPrice" + rowid).value = "";
                return;
            } else {

                // Convert to Yahoo String
                var stockName = str.substr(0, str.indexOf(" "));
                var stockCountry = str.substr(str.indexOf(" ") + 1);
                var yahooStr = stockName;
                if (stockCountry == "US") {
                    document.getElementById("country" + rowid).value = "usa";
                    document.getElementById("country" + rowid).disabled = true;
                } else if (stockCountry == "CN") {
                    document.getElementById("country" + rowid).value = "canada";
                    document.getElementById("country" + rowid).disabled = true;
                    var yahooStr = yahooStr + ".TO";
                } else {
                    document.getElementById("country" + rowid).disabled = false;
                }

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
                        //alert(xmlhttp.responseText);
                        stockPrice = responseJson.query.results.quote.LastTradePriceOnly;
                        document.getElementById("limitPrice" + rowid).value = stockPrice;
                        document.getElementById("limitPrice" + rowid).disabled = true;

                        document.getElementById("securityName" + rowid).value = responseJson.query.results.quote.Name;
                        document.getElementById("securityName" + rowid).disabled = true;

                        if (document.getElementById("side" + rowid).value == "sell" && (document.getElementById("maxShares" + rowid).value == "" || document.getElementById("maxShares" + rowid).value == "0")) {
                            populateMaxShares(rowid);
                        }

                        //Get the ISIN Number from DB
                        populateStockInfoFromDB(rowid);
                        updateRowTotal(rowid);
                    }
                };

                //alert("Sending : getSecurityInfo.php?sym=" + str);
                xmlhttp.open("GET", "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20%3D%20%22" + yahooStr + "%22&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=", true);
                xmlhttp.send();
            }
        }

        function mktLimitChanged(rowid) {
            var mkt_or_limit = document.getElementById("mkt_or_limit" + rowid).value;
            if (mkt_or_limit == "mkt") {
                getStockInfoYahoo(rowid);
            } else {
                document.getElementById("limitPrice" + rowid).value = "0";
                document.getElementById("limitPrice" + rowid).disabled = false;
                updateRowTotal(rowid);
            }
        }

        function getExchangeRate() {

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
                    var exchangeRate = responseJson.query.results.rate.Rate;
                    EXCHANGE_RATE = responseJson.query.results.rate.Rate;
                    document.getElementById("exchangeRate").value = exchangeRate;
                    updateToCurrValue();
                }
            };
            xmlhttp.open("GET", "https://query.yahooapis.com/v1/public/yql?q=select%20Rate%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22USDCAD%22)%3B&format=json&diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=", true);
            xmlhttp.send();
        }

        function updateToCurrValue() {
            var fromValue = document.getElementById("fromValue").value;
            var exchangeRate = document.getElementById("exchangeRate").value;
            var toValue = Math.round(fromValue * exchangeRate * 100) / 100;
            document.getElementById("toValue").value = toValue;
            colorizeTotals();
            colorizeCurrencyConv();
        }

        function updateFromCurrValue() {
            var toValue = document.getElementById("toValue").value;
            var exchangeRate = document.getElementById("exchangeRate").value;
            var fromValue = Math.round(toValue / exchangeRate * 100) / 100;
            document.getElementById("fromValue").value = fromValue;
            colorizeTotals();
            colorizeCurrencyConv();
        }

        function switchCurr() {
            document.getElementById("fromValue").value = "0";

            var fromCurrency = document.getElementById("fromCurrency").value;

            if (fromCurrency == "USD") {
                document.getElementById("fromCurrency").value = "CAD";
                document.getElementById("toCurrency").value = "USD";
            } else {
                document.getElementById("fromCurrency").value = "USD";
                document.getElementById("toCurrency").value = "CAD";
            }
            var exchange_rate = document.getElementById("exchangeRate").value;

            if (exchange_rate != 0) {
                document.getElementById("exchangeRate").value = Math.round(1 / document.getElementById("exchangeRate").value * 10000) / 10000;
            } else {
                document.getElementById("exchangeRate").value = 1;
            }
            updateToCurrValue();
            setMaxFromConv();
        }

        ERROR_STATUS_CONV = false;
        ERROR_STATUS_TOTAL_CAD = false;
        ERROR_STATUS_TOTAL_USD = false;

        function colorizeCurrencyConv() {
            var currConvReqValue = parseFloat(document.getElementById("fromValue").value, "10");

            var cashAtHandCAD = parseFloat(document.getElementById("cashAtHandCAD").value, "10");
            var cashAtHandUSD = parseFloat(document.getElementById("cashAtHandUSD").value, "10");

            var fromCurrency = document.getElementById("fromCurrency").value;

            if (fromCurrency == "USD") {
                if (currConvReqValue > cashAtHandUSD) {
                    document.getElementById("fromValue").style.backgroundColor = "red";
                    document.getElementById("fromValue").style.color = "white";
                    document.getElementById("cashAtHandUSD").style.backgroundColor = "red";
                    document.getElementById("cashAtHandUSD").style.color = "white";

                    ERROR_STATUS_CONV = true;
                } else {
                    document.getElementById("fromValue").style.backgroundColor = "";
                    document.getElementById("fromValue").style.color = "";
                    document.getElementById("cashAtHandUSD").style.backgroundColor = "";
                    document.getElementById("cashAtHandUSD").style.color = "";

                    ERROR_STATUS_CONV = false;
                }
            } else {
                if (currConvReqValue > cashAtHandCAD) {
                    document.getElementById("fromValue").style.backgroundColor = "red";
                    document.getElementById("fromValue").style.color = "white";
                    document.getElementById("cashAtHandCAD").style.backgroundColor = "red";
                    document.getElementById("cashAtHandCAD").style.color = "white";

                    ERROR_STATUS_CONV = true;
                } else {
                    document.getElementById("fromValue").style.backgroundColor = "";
                    document.getElementById("fromValue").style.color = "";
                    document.getElementById("cashAtHandCAD").style.backgroundColor = "";
                    document.getElementById("cashAtHandCAD").style.color = "";

                    ERROR_STATUS_CONV = false;
                }
            }
        }

        function prepareCurrencyConvTable() {
            if (CURRENCY_CONV_REQUESTED) {
                var table = document.getElementById("prepped_conv_table_tbody");
                table.innerHTML = "";
                var row = table.insertRow(table.rows.length);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);

                cell1.innerHTML = "From";
                cell1.style = "vertical-align: middle;";

                cell2.innerHTML = document.getElementById("fromValue").value;
                cell3.innerHTML = document.getElementById("fromCurrency").value;

                var row = table.insertRow(table.rows.length);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);

                cell1.innerHTML = "<span style=\"font-size: 11px;\">Exchange Rate</span>";
                cell2.innerHTML = document.getElementById("exchangeRate").value;
                cell3.innerHTML = "";

                var row = table.insertRow(table.rows.length);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);

                cell1.innerHTML = "To";
                cell1.style = "vertical-align: middle;";
                cell2.innerHTML = document.getElementById("toValue").value;
                cell3.innerHTML = document.getElementById("toCurrency").value;

                document.getElementById("preppedCurrencyConvDiv").style.display = 'block';

            } else {
                document.getElementById("preppedCurrencyConvDiv").style.display = 'none';
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
                <div class="navbar-header pull-right">

                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <p class="navbar-text">
                    <b> <?php echo $_SESSION['user_email']; ?> </b>
                     <?php echo $_SESSION['trading_group']; ?> 
                    <ul class="nav navbar-nav">
                        <li><a href="?logout">Logout</a></li>
                    </ul>
                </p>
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
                                <h2>Tradesheet</h2>
                                <h3><?php echo $_SESSION['trading_group']; ?></h3>
                            </div>
                            <div class="panel-body">
                                <p class="text-right">
                                    <button type="button" class="btn btn-default" onclick="addRow();">Add Row</button>
                                    <button type="button" class="btn btn-default" onclick="location.href='?logout'">|| Logout ||</button>
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
                                            <td>
                                                <input class="form-control" id="symbol1" type="text" onkeyup="javascript:capitalize(this.id, this.value);" onblur="getStockInfoYahoo(1);">
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
                                                <input class="form-control" id="shares1" type="number" value="0" min="0" onblur="validateShareCount(1);" onchange="updateRowTotal(1);" onkeyup="updateRowTotal(1);" disabled>
                                            </td>
                                            <td>
                                                <input class="form-control" id="maxShares1" value="0" onchange="validateShareCount(1);" disabled>
                                            </td>
                                            <td>
                                                <select id="orderType1">
                                                    <option value="gtc">GTC</option>
                                                    <option value="day">Day</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="mkt_or_limit1" onchange="mktLimitChanged(1);">
                                                    <option value="mkt">MKT</option>
                                                    <option value="limit">Limit</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="form-control" id="limitPrice1" type="number" min="0" value="0" onchange="updateRowTotal(1);" disabled>
                                            </td>
                                            <td>
                                                <input class="form-control text-right" id="total1" type="number" value="0" disabled>
                                            </td>
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
                                                        <input class="form-control text-left" id="fromCurrency" type="text" value="CAD" disabled>
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
                                                        <input class="form-control text-left" id="toCurrency" type="text" value="USD" disabled>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" class="btn btn-default" onclick="startOver();">Clear All</button>
                                    <button type="button" class="btn btn-primary" onclick="prepareSheet();">Prepare Sheet</button>


                                </div>
                                <div style="clear: both;">



                                    <div id="preparedSheetDiv" style="display: none; padding-top: 20px;">
                                        <table id="preparedTable" class="table table-striped table-bordered">
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

                                        <div id="preppedCurrencyConvDiv" style="display: block; padding-top: 20px; max-width: 450px; margin: auto;">
                                            <table id="prepped_conv_table" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th colspan="3" style="text-align: center;">Currency Conversion Request</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="prepped_conv_table_tbody">
                                                </tbody>
                                            </table>
                                        </div>

                                        <table id="email_table" class="table table-striped table-bordered" style="width:300;">
                                            <tbody>
                                                <tr>
                                                    <td>McGill ID:</td>
                                                    <td>
                                                        <input class="form-control" id="userEmail" type="email" placeholder="Your McGill email address">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Password:</td>
                                                    <td>
                                                        <input class="form-control" id="sheetSaleTotalUSD" type="password">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <button type="button" class="btn btn-primary" onclick="sendEmail();">Send Email</button>
                                        <button type="button" class="btn btn-default" onclick="startOver();">Start Over</button>

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