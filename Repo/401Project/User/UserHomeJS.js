$(document).ready(function(){

    $.when(getFiscalYears()).done(generateReport);

    var report_info=[];  // array of objects that contain report information definded in database
    var dropDownType = 0;

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
    function getReportType () {
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
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to retrieve report types");
            }
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
            }
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
        var yearTypeKey = "";
        if (yearTypeStr.toUpperCase()=="Specific Year".toUpperCase()) {
            yearTypeKey = "specific";
            $.ajax({
                url:"../lib/php/usr/reportCommunicator.php",
                type: "POST",
                data: {
                    yearTypeKey:yearTypeKey,
                    specificYearInt:specificYearInt},
                success:function(){
                    var parent = $("embed#pdfBox").parent();
                    var newElement = "<embed id='pdfBox' src='"+"../lib/php/usr/Report_userSpecificYear.php"+"' width='100%' height='800px'></embed>";
                    $("embed#pdfBox").remove();
                    parent.append(newElement);
                    // Change Download Button Source

                },
                error: function(xhr, status, error){
                    alert("Fail to connect to the server when generaeting the report");
                }
            });
        }
        else if (yearTypeStr.toUpperCase()=="Completed Course".toUpperCase()) {
            if(fromYearInt>toYearInt) {
                alert("Invalid year range");
                return;
            }
            yearTypeKey = "range";
            $.ajax({
                url:"../lib/php/usr/reportCommunicator.php",
                type: "POST",
                data: {
                    yearTypeKey:yearTypeKey,
                    toYearInt:toYearInt,
                    fromYearInt:fromYearInt},
                success:function(){
                    var parent = $("embed#pdfBox").parent();
                    var newElement = "<embed id='pdfBox' src='"+"../lib/php/usr/Report_userCompletedCourse.php"+"' width='100%' height='800px'></embed>";
                    $("embed#pdfBox").remove();
                    parent.append(newElement);
                    // Change Download Button Source

                },
                error: function(xhr, status, error){
                    alert("Fail to connect to the server");
                }
            });
        }
        else if (yearTypeStr.toUpperCase()=="Annual Totals".toUpperCase()) {
            var parent = $("embed#pdfBox").parent();
            var newElement = "<embed id='pdfBox' src='"+"../lib/php/usr/Report_userAnnualTotals.php"+"' width='100%' height='800px'></embed>";
            $("embed#pdfBox").remove();
            parent.append(newElement);

        }

    }


    //Hiding subset of filters from result of changing result type selection
    $("#yearTypeSelect").change(function () {
        var str = "";
        $( "#yearTypeSelect option:selected" ).each(function() {
            str = $( this ).text();
        });
        if (str.toUpperCase()=="Specific Year".toUpperCase()) {
            $(".filterListCol p, .dropDownFilter").show();
            $("#fromYearLabel, #toYearLabel").hide();
            $("#fromYearDiv, #toYearDiv").hide();
        }
        else if (str.toUpperCase()=="Completed Course".toUpperCase()){
            $(".filterListCol p, .dropDownFilter").show();
            $("#specificYearLabel, #specificYearDiv").hide();
        }
        else {
            $("#specificYearLabel, #specificYearDiv").hide();
            $("#fromYearLabel, #toYearLabel").hide();
            $("#fromYearDiv, #toYearDiv").hide();
        }
    }).change();


    $("#genReportBtn").click(generateReport);



});

