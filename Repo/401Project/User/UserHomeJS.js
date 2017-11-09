$(document).ready(function(){

    //$.when(getReportType()).done(generateReport);

    $("#homeTab").on("click",function(e) {
        e.preventDefault();
    });

    var report_info=[];  // array of objects that contain report information definded in database
    var download_reportType = "";
    var download_year0 = -1;
    var download_year1 = -1;
    var download_year2 = -1; // For downloading

    function getDropDownType(name) {
        for (var i = 0; i < report_info.length; i++)
            if (report_info[i][0] == name)
                return report_info[i][1];
    }
    function getReportFileName(name) {
        for (var i = 0; i < report_info.length; i++)
            if (report_info[i][0] == name)
                return report_info[i][2];
    }

    getReportType();
    // This function runs when Admin logs into the page and update dropdown for report types
    function getReportType() {
        $.ajax({
            url:"../lib/php/usr/getReportType.php",
            type: "POST",
            dataType: "json",
            success:function(results){
                var size = results.length;
                var temp;
                for (var i = 0; i < size; i++) {
                    var report = results[i];
                    report_info.push(report);
                    var type = report[0];
                    temp += "<option>"+type+"</option>";
                }
                // update drop down selections - reportTypeSelect
                var parent = $("select#yearTypeSelect").parent();
                var newElement = '<select id="yearTypeSelect">'+temp+'</select>';
                $("select#yearTypeSelect").remove();
                parent.append(newElement);

                getFiscalYears();
                generateReport()
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to retrieve report types");
            },
            async:false
        });
    }

    // Run as user enter the page, update years selections in drop downs
    function getFiscalYears () {
        return $.ajax({
            url:"../lib/php/usr/getFiscalYears.php",
            type: "POST",
            dataType: "json",
            success:function(results){
                var size = results.length;
                var temp;
                for (var i = size-1; i >= 0; i--) {
                    var fiscalyear = results[i];
                    temp += "<option>"+fiscalyear+"</option>";
                }
                // update drop down selections - specific year
                var parent = $("select#specificYearSelect").parent();
                var newElement = '<select id="specificYearSelect">'+temp+'</select>';
                $("select#specificYearSelect").remove();
                parent.append(newElement);

                // update drop down selections - year range
                // from year
                parent = $("select#fromYearSelect").parent();
                newElement = '<select id="fromYearSelect">'+temp+'</select>';
                $("select#fromYearSelect").remove();
                parent.append(newElement);

                // to year
                parent = $("select#toYearSelect").parent();
                newElement = '<select id="toYearSelect">'+temp+'</select>';
                $("select#toYearSelect").remove();
                parent.append(newElement);

            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to fetch available years");
            },
            async:false
        });
    }

    // Button Click to generate corresponding reports
    // By Default generate most up-to-date's report
    function generateReport() {
        var toYearVal = $("#toYearSelect").val();
        var fromYearVal = $("#fromYearSelect").val();
        var specificYearVal = $("#specificYearSelect").val();

        //Convert value of year from str to int
        var toYearInt = parseInt(toYearVal);
        var fromYearInt = parseInt(fromYearVal);
        var specificYearInt = parseInt(specificYearVal);

        //Get selected report type
        var yearTypeStr = $("#yearTypeSelect").val();
        var yearTypeKey = getDropDownType(yearTypeStr);
        if (yearTypeKey == 2) {
            if (toYearInt < fromYearInt) {
                alert("Invalid year range!");
                return;
            }
        }
        var file_name = getReportFileName(yearTypeStr);
        $.ajax({
            url:"../lib/php/usr/reportCommunicator.php",
            type: "POST",
            data: {
                yearTypeKey:yearTypeKey, // # of year input
                specificYearInt:specificYearInt,
                toYearInt:toYearInt,
                fromYearInt:fromYearInt,
                file_name:file_name,
            },
            success:function(){
                var parent = $("embed#pdfBox").parent();

                var newElement = "<embed id='pdfBox' src='"+"../lib/php/usr/report/"+file_name+"' width='100%' height='800px'></embed>";
                $("embed#pdfBox").remove();
                parent.append(newElement);

                download_reportType = yearTypeStr;
                download_year0 = specificYearInt;
                download_year1 = fromYearInt;
                download_year2 = toYearInt;
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when generaeting the report");
            }
        });
    }

    function getCurrentBalance () {
        $.ajax({
            url:"../lib/php/usr/getCurrentBalance.php",
            type: "POST",
            dataType: "json",
            success:function(result){
                var balance = result;
                var info;
                if (balance >= 0 ) {
                    info = "Requirement has been met (Extra: " + balance + " Hours)";
                } else {        // balance < 0
                    info = balance;
                }

                // Update info to the div
                document.getElementById('hoursNeedLabel').innerHTML = 'Hours needed: '+info;
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to fetch current balance");
            },
            async:false
        });
    }

    //Hiding subset of filters from result of changing result type selection
    $("#yearTypeSelect").change(function () {
        var str = $( "#yearTypeSelect option:selected" ).text();

        for(var i=0;i<report_info.length;i++) {
            if(str.toUpperCase()==report_info[i][0].toUpperCase()) {
                $("#reportDescription").find("p")[0].innerHTML = report_info[i][3];
            }
        }

        var num = getDropDownType(str);
        if (num == 1) {
            $(".filterListCol p, .dropDownFilter").show();
            $("#fromYearLabel, #toYearLabel").hide();
            $("#fromYearDiv, #toYearDiv").hide();
        }
        else if (num == 2){
            $(".filterListCol p, .dropDownFilter").show();
            $("#specificYearLabel, #specificYearDiv").hide();
        }
        else if (num == 0) {
            $("#specificYearLabel, #specificYearDiv").hide();
            $("#fromYearLabel, #toYearLabel").hide();
            $("#fromYearDiv, #toYearDiv").hide();
        }
    }).change();


    $("#genReportBtn").click(generateReport);

    // $("#Download").click(function(){
    //     if (download_reportType == "") {
    //         alert("Please generate a report to download.");
    //         return;
    //     }
    //     else {
    //         var file_name = getReportFileName(download_reportType);
    //         $.ajax({
    //             url:"../lib/php/usr/downloadCommunicator.php",
    //             type: "POST",
    //             data: {
    //                 download:true,
    //                 file_name:file_name,
    //             },
    //             success:function(){
    //             },
    //             error: function(xhr, status, error){
    //                 alert("Fail to connect to the server when generaeting the report");
    //             }
    //         });
    //     }
    // });



});

