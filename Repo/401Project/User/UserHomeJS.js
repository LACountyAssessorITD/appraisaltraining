$(document).ready(function(){

    $.when(getFiscalYears()).done(generateReport);

    // Run as user enter the page, update years selections in drop downs
    function getFiscalYears () {
        return $.ajax({
            url:"lib/php/getFiscalYears.php",
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
                url:"lib/php/reportCommunicator.php",
                type: "POST",
                data: {
                    yearTypeKey:yearTypeKey,
                    specificYearInt:specificYearInt},
                success:function(){
                    var parent = $("embed#pdfBox").parent();
                    var newElement = "<embed id='pdfBox' src='"+"lib/php/singleUserReport.php"+"' width='100%' height='800px'></embed>";
                    $("embed#pdfBox").remove();
                    parent.append(newElement);
                    // Change Download Button Source

                },
                error: function(xhr, status, error){
                    alert("Fail to connect to the server");
                }
            });
        }
        else {
            yearTypeKey = "range";
            $.ajax({
                url:"lib/php/reportCommunicator.php",
                type: "POST",
                data: {
                    yearTypeKey:yearTypeKey,
                    toYearInt:toYearInt,
                    fromYearInt:fromYearInt},
                success:function(){
                    var parent = $("embed#pdfBox").parent();
                    var newElement = "<embed id='pdfBox' src='"+"lib/php/singleUserReport.php"+"' width='100%' height='800px'></embed>";
                    $("embed#pdfBox").remove();
                    parent.append(newElement);
                    // Change Download Button Source

                },
                error: function(xhr, status, error){
                    alert("Fail to connect to the server");
                }
            });
        }


    }


    // $("#genReportBtn").click(function(){
    //     alert("123123123");
    //     //Get string result from selection (Years)
    //     var toYearVal = $("#toYearSelect").val();
    //     var fromYearVal = $("#fromYearSelect").val();
    //     var specificYearVal = $("#specificYearSelect").val();

    //     //Convert value of year from str to int
    //     var toYearInt = parseInt(toYearVal);
    //     var fromYearInt = parseInt(fromYearVal);
    //     var specificYearInt = parseInt(specificYearVal);

    //     //Get selected report type
    //     var yearTypeStr = $("#yearTypeSelect").val();
    //     var yearTypeKey = "";
    //     if (yearTypeStr.toUpperCase()=="Specific Year".toUpperCase()) {
    //         yearTypeKey = "specific";
    //     }
    //     else {
    //         yearTypeKey = "range";
    //     }

    //     $.ajax({
    //         url:"lib/php/reportCommunicator.php",
    //         type: "POST",
    //         data: {
    //             yearTypeKey:yearTypeKey,
    //             specificYearInt:specificYearInt},
    //         success:function(){
    //             var parent = $("embed#pdfBox").parent();
    //             var newElement = "<embed id='pdfBox' src='"+"lib/php/singleUserReport.php"+"' width='100%' height='800px'></embed>";
    //             $("embed#pdfBox").remove();
    //             // var link ='<a href="singleUserReport.php" target="_blank">If you have trouble viewing the file, click here</a>';
    //             // parent.append(link);
    //             parent.append(newElement);

    //             // Change Download Button Source
    //             //getFiscalYears();
    //         },
    //         error: function(xhr, status, error){
    //             alert("Fail to connect to the server");
    //         }
    //     });
    // });

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
        else {
            $(".filterListCol p, .dropDownFilter").show();
            $("#specificYearLabel, #specificYearDiv").hide();
        }
    }).change();


    $("#genReportBtn").click(generateReport);

});

