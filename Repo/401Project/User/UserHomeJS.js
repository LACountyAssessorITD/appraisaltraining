$(document).ready(function(){

    $.when(getFiscalYears()).done(generateReport);

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
            // Change Download Button Source

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

