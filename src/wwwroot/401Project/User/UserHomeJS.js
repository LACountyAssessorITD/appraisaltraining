// function getFiscalYears () {
//     $.ajax({
//         url:"lib/php/getFiscalYears.php",
//         type: "POST",
//         dataType: "json",
//         success:function(results){
//             var size = results.length;
//             var temp;
//             for (var i = size-1; i >= 0; i--) {
//                 var fiscalyear = results[i];
//                 temp += "<option>"+fiscalyear+"</option>";
//             }
//             // Do something. Note that it is a string with year range. For instance insteade of 2016, it returns 2016-2017
//             // For instance updating
//             var parent = $("select#specificYearSelect").parent();
//             var newElement = '<select id="specificYearSelect">'+temp+'</select>';
//             $("select#specificYearSelect").remove();
//             parent.append(newElement);
//         },
//         error: function(xhr, status, error){
//             alert("Fail to connect to the server when trying to fetch available years");
//         }
//     });
// }

// function generateReport() {
//     alert("beforegetyear");
//     getFiscalYears();
//     alert("aftergetyear");
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
//             parent.append(newElement);
//             // Change Download Button Source

//         },
//         error: function(xhr, status, error){
//             alert("Fail to connect to the server");
//         }
//     });
// }




// $(document).ready(getFiscalYears());


$(document).ready(function(){

    function getFiscalYears () {
        $.ajax({
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
                // Do something. Note that it is a string with year range. For instance insteade of 2016, it returns 2016-2017
                // For instance updating
                var parent = $("select#specificYearSelect").parent();
                var newElement = '<select id="specificYearSelect">'+temp+'</select>';
                $("select#specificYearSelect").remove();
                parent.append(newElement);
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to fetch available years");
            }
        });
    }

    function generateReport() {
        // alert("beforegetyear");
        getFiscalYears();
        alert("aftergetyear");
        //Get string result from selection (Years)
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
        }
        else {
            yearTypeKey = "range";
        }

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





    $("#genReportBtn").click(function(){
        alert("123123123");
        //Get string result from selection (Years)
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
        }
        else {
            yearTypeKey = "range";
        }

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
                // var link ='<a href="singleUserReport.php" target="_blank">If you have trouble viewing the file, click here</a>';
                // parent.append(link);
                parent.append(newElement);

                // Change Download Button Source
                //getFiscalYears();
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server");
            }
        });
    });

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



    // alert("beforetrigger");
    $("#genReportBtn").click(generateReport());
    // alert("aftertrigger");

    // $("#genReportBtn").click(generateReport());
    // $("#genReportBtn").trigger("click");

});

// $(document).ready(generateReport());

