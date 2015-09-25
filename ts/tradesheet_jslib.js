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
    cell10.innerHTML = "<input class=\"form-control text-right\" id=\"total" + row_num + "\" type=\"text\" disabled>";

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
        };

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
    oInputs = document.getElementsByTagName('input') // store collection of all <input/> elements
    var sheetTotalPurchaseCAD = 0
    var sheetTotalPurchaseUSD = 0
    var sheetTotalSaleCAD = 0
    var sheetTotalSaleUSD = 0
    var calc_row_num;

    for (i = 0; i < oInputs.length; i++) {
        // loop through and find <input type="text"/>
        if (oInputs[i].type == 'text' && oInputs[i].id.substring(0, 5) == 'total') {
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

    if (sheetTotalPurchaseCAD > parseFloat(document.getElementById("cashAtHandCAD").value, "10")) {
        document.getElementById("sheetPurchaseTotalCAD").style.backgroundColor = "red";
        document.getElementById("sheetPurchaseTotalCAD").style.color = "white";
    } else {
        document.getElementById("sheetPurchaseTotalCAD").style.backgroundColor = "";
        document.getElementById("sheetPurchaseTotalCAD").style.color = "";
    }
    if (sheetTotalPurchaseUSD > parseFloat(document.getElementById("cashAtHandUSD").value, "10")) {
        document.getElementById("sheetPurchaseTotalUSD").style.backgroundColor = "red";
        document.getElementById("sheetPurchaseTotalUSD").style.color = "white";
    } else {
        document.getElementById("sheetPurchaseTotalUSD").style.backgroundColor = "";
        document.getElementById("sheetPurchaseTotalUSD").style.color = "";
    }

}

function prepareSheet() {
    
    var test = 1;
    
    alert("hi");
   
    
//    while ( $prep_row_num < 4 ) {
//       
//        prep_row_num++;
//
//        var table = document.getElementById("preparedTable");
//        var row = table.insertRow(table.rows.length);
//        var cell1 = row.insertCell(0);
//        var cell2 = row.insertCell(1);
//        var cell3 = row.insertCell(2);
//        var cell4 = row.insertCell(3);
//        var cell5 = row.insertCell(4);
//        var cell6 = row.insertCell(5);
//        var cell7 = row.insertCell(6);
//        var cell8 = row.insertCell(7);
//        var cell9 = row.insertCell(8);
//        var cell10 = row.insertCell(9);
//
//        
//        cell1.innerHTML  = document.getElementById("symbol" + $prep_row_num).value;
//        cell2.innerHTML  = document.getElementById("isinNumber" + $prep_row_num).value;
//        cell3.innerHTML  = document.getElementById("securityName" + $prep_row_num).value;
//        cell4.innerHTML  = document.getElementById("country" + $prep_row_num).value;
//        cell5.innerHTML  = document.getElementById("side" + $prep_row_num).value;
//        cell6.innerHTML  = document.getElementById("shares" + $prep_row_num).value;
//        cell7.innerHTML  = document.getElementById("maxShares" + $prep_row_num.value);
//        cell8.innerHTML  = document.getElementById("orderType" + $prep_row_num).value;
//        cell9.innerHTML  = document.getElementById("limitPrice" + $prep_row_num).value;
//        cell10.innerHTML = document.getElementById("total" + $prep_row_num).value;
//   
//    }
//    alert("2");
    
   // document.getElementById("preparedSheetDiv").style.display = 'block';
}