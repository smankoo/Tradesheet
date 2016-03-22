function addRow() {
    var table = document.getElementById("stocks_table_tbody");

    row_num = table.rows.length;
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

    cell1.innerHTML = "<input class=\"form-control\" id=\"symbol" + row_num + "\" type=\"text\" onkeyup=\"javascript:capitalize(this.id, this.value);\" onblur=\"populateStockInfo(" + row_num + ");\">";
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

function populateStockInfo(rowid) {
    //Get the ISIN Number from DB
    populateStockInfoFromDB(rowid);
    getStockInfoYahoo(rowid);
}

function populateStockInfoFromDB(rowid) {
    var str = document.getElementById("symbol" + rowid).value;

    if (str == "") {
        document.getElementById("isinNumber" + rowid).value = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttpDB = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttpDB = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttpDB.onreadystatechange = function () {
            if (xmlhttpDB.readyState == 4 && xmlhttpDB.status == 200) {
                if (xmlhttpDB.responseText == "NOTCURRENT") {
                    alert("Stale portfolio found. Contact Admin.");
                } else if (xmlhttpDB.responseText == "NOTFOUND") {

                    document.getElementById("isinNumber" + rowid).value = "";
                    document.getElementById("isinNumber" + rowid).disabled = false;

                    document.getElementById("maxShares" + rowid).value = "0";
                    document.getElementById("shares" + rowid).max = "0";

                } else {

                    var responseJson = JSON.parse(xmlhttpDB.responseText);

                    document.getElementById("isinNumber" + rowid).value = responseJson.isin;
                    document.getElementById("isinNumber" + rowid).disabled = true;

                    if (document.getElementById("side" + rowid).value == "sell" && (document.getElementById("maxShares" + rowid).value == "" || document.getElementById("maxShares" + rowid).value == "0")) {
                        populateMaxShares(rowid);
                    }
                }
            }
        };

        // Reset the row before getting new info
        //                document.getElementById("isinNumber"      + rowid).value = "";
        //                document.getElementById("securityName"    + rowid).value = "";
        //                document.getElementById("country"         + rowid).value = "";
        //                document.getElementById("side"            + rowid).value = "";
        //                document.getElementById("shares"          + rowid).value = 0;
        //                document.getElementById("maxShares"       + rowid).value = 0;
        //                document.getElementById("orderType"       + rowid).value = "gtc";
        //                document.getElementById("mkt_or_limit"    + rowid).value = "mkt";
        //                document.getElementById("limitPrice"      + rowid).value = 0;
        //                document.getElementById("total"           + rowid).value = 0;
        //                
        
        xmlhttpDB.open("GET", "getSecurityInfo.php?sym=" + str, true);
        xmlhttpDB.send();
    }
}

function getStockInfoYahoo(rowid) {
    $('#securityName' + rowid).addClass('loadinggif');
    $("#limitPrice" + rowid).addClass('loadinggif');

    var str = document.getElementById("symbol" + rowid).value;

    // Check for blankness
    if (str == "") {
        document.getElementById("limitPrice" + rowid).value = "";
        return;
    } else {

        // Convert to Yahoo String
        var stockName = str.substr(0, str.indexOf(" "));
        var stockCountry = str.substr(str.indexOf(" ") + 1);
        // Convert / to - for yahoo lookup
        var yahooStr = stockName.replace("/", "-");;

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

                //document.getElementById('debugBox').textContent = document.getElementById('debugBox').textContent.append("Response from Yahoo : " . responseJson . "\n");

                stockPrice = responseJson.query.results.quote.LastTradePriceOnly;

                $('#limitPrice' + rowid).removeClass('loadinggif');

                document.getElementById("limitPrice" + rowid).value = stockPrice;
                document.getElementById("limitPrice" + rowid).disabled = true;

                $('#securityName' + rowid).removeClass('loadinggif');

                document.getElementById("securityName" + rowid).value = responseJson.query.results.quote.Name;
                document.getElementById("securityName" + rowid).disabled = true;

                if (document.getElementById("side" + rowid).value == "sell" && (document.getElementById("maxShares" + rowid).value == "" || document.getElementById("maxShares" + rowid).value == "0")) {
                    populateMaxShares(rowid);
                }

                updateRowTotal(rowid);
            }
        };

        var yahooQry = "getSecurityInfoYahoo.php?sym=" + yahooStr;
        //document.getElementById('debugBox').textContent = yahooQry;

        xmlhttp.open("GET", yahooQry, true);
        xmlhttp.send();
    }
}

function debugOut (message){
    document.getElementById('debugBox').textContent = document.getElementById('debugBox').textContent + message + "\n";
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

SHARE_COUNT_ERROR = false;

function validateShareCount(rowid) {
    var maxShares = parseFloat(document.getElementById("maxShares" + rowid).value, 10);
    var shareCount = parseFloat(document.getElementById("shares" + rowid).value, 10);
    if (!shareCount) {
        document.getElementById("shares" + rowid).style.backgroundColor = "";
        document.getElementById("shares" + rowid).style.color = "";
        return;
    }
    if (shareCount > maxShares) {
        document.getElementById("shares" + rowid).style.backgroundColor = "red";
        document.getElementById("shares" + rowid).style.color = "white";
        alert("Number of shares is higher than the number of shares you own");
    } else {
        document.getElementById("shares" + rowid).style.backgroundColor = "";
        document.getElementById("shares" + rowid).style.color = "";
    }
    validateSheet();
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
            calc_row_num = oInputs[i].id.substring(5);

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
    validateSheet();
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

    document.getElementById("sheetPurchaseTotalCAD").value = "0";
    document.getElementById("sheetPurchaseTotalUSD").value = "0";
    document.getElementById("sheetSaleTotalCAD").value = "0";
    document.getElementById("sheetSaleTotalUSD").value = "0";

}

function capitalize(textboxid, str) {
    var str = str.toUpperCase();
    document.getElementById(textboxid).value = str;
}


function getStockInfoYahoo_bak(rowid) {
    $('#securityName' + rowid).addClass('loadinggif');
    $("#limitPrice" + rowid).addClass('loadinggif');

    var str = document.getElementById("symbol" + rowid).value;


    // Check for blankness
    if (str == "") {
        document.getElementById("limitPrice" + rowid).value = "";
        return;
    } else {

        // Convert to Yahoo String
        var stockName = str.substr(0, str.indexOf(" "));
        var stockCountry = str.substr(str.indexOf(" ") + 1);
        // Convert / to - for yahoo lookup
        var yahooStr = stockName.replace("/", "-");;


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

                //document.getElementById('debugBox').textContent = document.getElementById('debugBox').textContent.append("Response from Yahoo : " . responseJson . "\n");

                stockPrice = responseJson.query.results.quote.LastTradePriceOnly;

                $('#limitPrice' + rowid).removeClass('loadinggif');

                document.getElementById("limitPrice" + rowid).value = stockPrice;
                document.getElementById("limitPrice" + rowid).disabled = true;

                $('#securityName' + rowid).removeClass('loadinggif');

                document.getElementById("securityName" + rowid).value = responseJson.query.results.quote.Name;
                document.getElementById("securityName" + rowid).disabled = true;

                if (document.getElementById("side" + rowid).value == "sell" && (document.getElementById("maxShares" + rowid).value == "" || document.getElementById("maxShares" + rowid).value == "0")) {
                    populateMaxShares(rowid);
                }

                updateRowTotal(rowid);
            }
        };

        var yahooQry = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20%3D%20%22" + yahooStr + "%22&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=";
        document.getElementById('debugBox').textContent = yahooQry;

        xmlhttp.open("GET", yahooQry, true);
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

        var table = document.getElementById("prepped_conv_table_thead");
        table.innerHTML = "<tr><th colspan=\"3\" style=\"text-align: center;\">Currency Conversion Request</th></tr>";

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

function pageloadCheckPortfolio() {

    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var portfolioStatus = xmlhttp.responseText;
            if (portfolioStatus == "CURRENT") {
                // If portfolio is not current, display the upload portfolio button
                document.getElementById("upload_portfolio_div").style.display = 'none';
            } else {
                // If portfolio is current, hide the upload portfolio button
                document.getElementById("upload_portfolio_div").style.display = 'block';
            }
        }
    };

    xmlhttp.open("GET", "checkPortfolio.php", true);
    xmlhttp.send();
}

function prepareSalePurchaseTables() {
    var table = document.getElementById("purchase_table_prepped_tbody");
    table.innerHTML = "";
    var row = table.insertRow(table.rows.length);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);

    cell1.innerHTML = "CAD";
    cell1.style = "vertical-align: middle;";

    cell2.innerHTML = document.getElementById("cashAtHandCAD").value;
    cell3.innerHTML = document.getElementById("sheetPurchaseTotalCAD").value;

    var row = table.insertRow(table.rows.length);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);

    cell1.innerHTML = "USD";
    cell1.style = "vertical-align: middle;";

    cell2.innerHTML = document.getElementById("cashAtHandUSD").value;
    cell3.innerHTML = document.getElementById("sheetPurchaseTotalUSD").value;

    var table = document.getElementById("sale_table_prepped_tbody");
    table.innerHTML = "";
    var row = table.insertRow(table.rows.length);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);

    cell1.innerHTML = "CAD";
    cell1.style = "vertical-align: middle;";

    cell2.innerHTML = document.getElementById("sheetSaleTotalCAD").value;

    var row = table.insertRow(table.rows.length);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);

    cell1.innerHTML = "USD";
    cell1.style = "vertical-align: middle;";
    cell2.innerHTML = document.getElementById("sheetSaleTotalUSD").value;
}

function sendEmail() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var emailStatus = xmlhttp.responseText;
            alert(emailStatus);
            if (portfolioStatus == "SUCCESS") {
                // 
                alert("Mail sent successfully");
            } else {
                // 
                alert("Error while sending email");
            }
        }
    };

    var emailBody = document.getElementById("email_body_div").innerHTML;


    alert("Sending email");
    xmlhttp.open("POST", "sendEmail.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("user_email=" + document.getElementById("userEmail").value + "&user_pass=" + document.getElementById("userPass").value + "&email_body=" + encodeURIComponent(emailBody));
}

function validateSheet() {

    var ERROR_STATUS_SHARES = false;

    var oInputs = new Array();
    oInputs = document.getElementsByTagName('input'); // store collection of all <input/> elements

    var calc_row_num;

    for (i = 0; i < oInputs.length; i++) {
        // loop through and find <input type="number"/>
        if (oInputs[i].type == 'number' && oInputs[i].id.substring(0, 6) == 'shares') {
            calc_row_num = oInputs[i].id.substring(6, 7);

            if (document.getElementById("side" + calc_row_num).value == "sell" && (document.getElementById("shares" + calc_row_num).value > document.getElementById("maxShares" + calc_row_num).value)) {
                ERROR_STATUS_SHARES = true;
                break;
            }
        }
    }
    
    if (ERROR_STATUS_TOTAL_CAD || ERROR_STATUS_TOTAL_USD || ERROR_STATUS_SHARES) {
        document.getElementById('btnPrepareSheet').className = "btn btn-disabled";
        document.getElementById('btnPrepareSheet').onclick = errorInSheet;
    } else {
        document.getElementById('btnPrepareSheet').className = "btn btn-primary";
        document.getElementById('btnPrepareSheet').onclick = prepareSheet;
    }

}

function errorInSheet() {
    alert("Please correct the errors in the sheet");
}