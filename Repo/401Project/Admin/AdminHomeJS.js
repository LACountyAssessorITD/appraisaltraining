$(document).ready(function(){
    var current_fiscal_year = -1;

    $("#homeTab").on("click",function(e) {
        e.preventDefault();
    });

    $(".top, .content, .footer").hide();

    $("#lineOne").on("animationend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd", function() {
        $(".top, .content, .footer").fadeIn(500);
        $("#splashScreen").fadeOut(500);
    });

    loadDefaultPDF();

    function loadDefaultPDF() {
        var parent = $(".pdfView");
        var newElement = "<object id='pdfBox' data='../LACLogo.pdf' type='application/pdf' width='100%' height='600px'>\
                            <p>It appears you don't have Adobe Reader or PDF support in this web browser.\
                            <a href='singleUserReport.php'>Click here to download the PDF</a>. Or\
                            <a href='http://get.adobe.com/reader/' target='_blank'>click here to install Adobe Reader</a>.</p>\
                            <embed src='../LACLogo.pdf' type='application/pdf'>\
                        </object>";
        parent.append(newElement);
    }

    var report_info=[];  // array of objects that contain report information definded in database
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
            url:"../lib/php/admin/getReportType.php",
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
                // var parent = $("select#reportTypeSelect").parent();
                // var newElement = "<select id='reportTypeSelect'>'+temp+'</select><i class='fa fa-question-circle-o' aria-hidden='true'>';
                // $("select#reportTypeSelect").remove();
                // parent.append(newElement);
                $("#reportTypeSelect").append(temp);
                $("#reportType").find(".toolTip")[0].innerHTML = report_info[0][3];
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to retrieve report types");
            },
            async: false
        });
        checkTypeForDownload();
    }

    getCurrentFiscalYear();
    function getCurrentFiscalYear() {
        $.ajax({
            url:"../lib/php/admin/getCurrentFiscalYear.php",
            type: "POST",
            success:function(results){
                current_fiscal_year = results;
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to retrieve current year");
            },
            async: false
        });
    }

    loadFilterOptions();

    function loadFilterOptions() {
        // alert("atload1");
        $(".dropDownFilter").each(function() {
            // alert("atload2");
            if ($(this).hasClass("no_db")) {
                return true;
            }
            var filter_name = $(this).children(".dropDownBtn").attr("name");
            var filter_type = $(this).parent().attr("name");
            var result_array = [];
            $.ajax({
                url:"../lib/php/admin/getFilters.php",
                type: "POST",
                dataType: "json",
                data: {
                    filter_type:filter_type,
                    filter_name:filter_name,
                },
                success:function(results){
                    result_array = results;
                },
                error: function(xhr, status, error){
                    // alert("Fail to connect to the server when trying to retrieve report types");
                     alert(status);
                },
                async:false
            });


            // var result_array =  getFilterNameAndType(filter_name,filter_type);

            var thisObj = $(this);

            if(result_array.length==0) {
                // $("#homeTab").text("isLen0--1");
            }
            var DPBContHtml_top = "<div class='DPBCont'>\
                        <div class='tableWrap'>\
                            <form class='leftInput'>\
                                <input type='text' placeholder='Search..' autocomplete='off'>\
                            </form>\
                            <div class='filterContTableBG'></div>\
                            <button class='resetBtn'><i class='fa fa-times' aria-hidden='true'></i> Reset Search</button>\
                            <table class='filterContTable'>\
                                <col width='20'>\
                                <thead>\
                                    <tr>\
                                        <td><input type='checkbox' name='selectAll'></td>\
                                        <td>Select All</td>\
                                    </tr>\
                                </thead>\
                                <tbody>";

            var i;
            var htmlStr = '';
            for(i=0;i<result_array.length;i++) {
                if((result_array[i]+"").toUpperCase()=="NULL") {
                    htmlStr += "<tr><td><input type='checkbox' name='selected'></td>\
                            <td>None</td></tr>";
                }
                else {
                    htmlStr += "<tr><td><input type='checkbox' name='selected'></td>\
                            <td>"+result_array[i]+"</td></tr>";
                }
            }

            var DPBContHtml_bottom = "</tbody>\
                            </table>\
                        </div>\
                        <div class='filterDisplayList'>\
                            <label>Selections:</label>\
                            <ul></ul>\
                        </div>\
                        <iframe class='cover' src='about:blank'></iframe>\
                    </div>";

            var DPBContHtml = DPBContHtml_top + htmlStr + DPBContHtml_bottom;

            thisObj.append(DPBContHtml);
        });
        // setTimeout(checkTypeForDownload,0);
        // checkTypeForDownload();
    }

    addDownArrow();

    function addDownArrow() {
        var markup = "<span class='dropDownBtnDownArrow' style='margin-left:5px'><i class='fa fa-angle-down' aria-hidden='true'></i></span>"
        $(".dropDownBtn").each(function() {
            $(this).append(markup);
        });
    }


    function parseLDAPManager(manager) {
        var splitOne = manager.split('=');
        var splitTwo = splitOne.split('/,');
        return splitTwo[0]+" "+splitTwo[1];
    }


    var dropDownType = 0;

    //loadTable("qNelson", "1996", "0608", 7);

    function loadTable(name,certNo,empNo,balance) {
        // Get LDAP Information
        var email = "test@gmail.com";
        var manager = "Yining Huang";
        var phone = "123-123-0123";
        var department = "Best Department";
        var title = "CEO LOL";

        $.ajax({
            url:"../lib/php/LDAP/getLdapInfo.php",
            type: "POST",
            dataType: "json",
            data: {
                empNo:empNo,
            },
            success:function(results){
                email = results[1];
                manager = results[2].replace(/\\/g, ",");
                phone = results[4];
                department = results[5];
                title = results[6];
            },
            error: function(xhr, status, error){
                alert(empNo);
                alert(error);
            },
            async:false
        });



        // Change balance to hours short
        if (balance >= 0) {
            // Requirement met
            balance = 0;
        } else {
            balance = balance * (-1);
        }

        var optionHTML = "";
        $.ajax({
            url:"../lib/php/admin/getFiscalYears.php",
            type: "POST",
            dataType: "json",
            data: {
                certNo:certNo,
            },
            success:function(results){
                var size = results.length;
                for (var i = size-1; i >= 0; i--) {
                    var fiscalyear = results[i];
                    optionHTML += "<option>"+fiscalyear+"</option>";
                }

            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to fetch available years for "+certNo);
                return;
            },
            async:false
        });

        var selectYearUI = "<label>Year </label><select class='specificYear'>"+optionHTML+"</select>";
        var selectRangeUI = "<label>From </label><br><select class='fromYear'>"+optionHTML+"</select><br><label>To </label><br><select class='toYear'>"+optionHTML+"</select>";
        var selectUI = "";

        if(dropDownType == 1) {
            selectUI = selectYearUI;
        }
        else if(dropDownType == 2) {
            selectUI = selectRangeUI;
        }

        // var trHTML = "<tr>\
        //                 <td><input type='checkbox' name='selected'></td>\
        //                 <td class='nameinfo'>"+name+"</td>\
        //                 <td class='emailInfo'>"+email+"</td>\
        //                 <td class='certNoInfo'>"+certNo+"</td>\
        //                 <td class='yearSelect'>"+selectUI+"</td>\
        //                 <td><button class='viewReportBtn'><i class='fa fa-eye' aria-hidden='true'> View</i></button></td>\
        //             </tr>";

        var trHTML = "<tr>\
                        <td><input type='checkbox' name='selected'></td>\
                        <td class='infoInfo'>";
        var infoHTML = "<button class='infoHoverBtn'><i class='fa fa-user' aria-hidden='true'></i> Info</button>\
                        <div class='infoHoverDPBCont'>\
                            <ul class='info_ul_one'>\
                                <li>Name: "+name+"</li>\
                                <li class='certNoLi'>CertNo: <span class='certNoInfo'>"+certNo+"</span></li>\
                                <li class='empNoInfo'>EmpNo: "+empNo+"</li>\
                                <li class='emailLi'>Email: <span class='emailInfo'>"+email+"</span></li>\
                            </ul>\
                            <ul class='info_ul_two'>\
                                <li class='phonenumberLi'>Phone: "+phone+"</li>\
                                <li class='titleLi'>Title: "+title+"</li>\
                                <li class='departmentLi'>Department: "+department+"</li>\
                                <li class='managerLi'>Manager: "+manager+"</li>\
                            </ul>\
                            <iframe class='cover' src='about:blank'></iframe>\
                        </div>";
        var trSecondHTML = "</td>\
                        <td class='nameinfo'>"+name+"</td>\
                        <td class='hoursShortInfo'>"+balance+"</td>\
                        <td class='yearSelect'>"+selectUI+"</td>\
                        <td><button class='viewReportBtn'><i class='fa fa-eye' aria-hidden='true'> View</i></button></td>\
                    </tr>";

        var toAppend = trHTML+infoHTML+trSecondHTML;

        $("#overviewTable tbody").append(toAppend);
    }

    $(document).on("click",".infoHoverBtn", function() {
        var isClicked = false;
        if($(this).data("clicked")) {
            isClicked = true;
        }

        $(".infoHoverDPBCont").each(function() {
            $(this).hide();
            $(this).prev(".infoHoverBtn").data("clicked",false);
            // if($(this).data("clicked")) {
            //     $(this).next(".infoHoverDPBCont").hide();
            //     $(this).closest(".infoHoverBtn").data("clicked",false);
            // }
        });

        $(this).data("clicked",isClicked);

        if($(this).data("clicked")) {
            $(this).next(".infoHoverDPBCont").hide();
            $(this).data("clicked",false);
        }
        else {
            $(this).next(".infoHoverDPBCont").show();
            $(this).data("clicked",true);
        }
    });

    // $(document).on({
    //     mouseenter: function() {
    //         $(this).next(".infoHoverDPBCont").show();
    //         $(this).css("color","blue");
    //     },
    //     mouseleave: function() {
    //         $(this).next(".infoHoverDPBCont").hide();
    //         $(this).css("color","black");
    //     }
    // }, ".infoHoverBtn");


    // $(".infoHoverBtn").hover(function() {
    //     $(this).next(".DPBCont").show();
    // }, function(){
    //     $(this).next(".DPBCont").hide();
    // });

    function loadReportSelection() {
        //dropDownType should be read in from table
        var reportTypeName = $("#reportTypeSelect option:selected").val();
        dropDownType = getDropDownType(reportTypeName);
    }


    function applyFilter() {
        clearTable();
        $("#hoursShortDiv").find("input").each(function() {
            $(this).prop("checked",true);
        });
       // generate SQL query clause
        var query = "";                               //!!!!!!!!!!!!!!!!!!!
        var filterNum = 0;
        $(".dropDownFilter").each(function() {
            if($(this).hasClass("no_db")) {
                return true;
            }
            var filterDisplayList = $(this).find(".filterDisplayList");
            var filter_name = $(this).children(".dropDownBtn").attr("name");
            var filter_type = $(this).parent().attr("name");
            var list = filterDisplayList.children("ul");
            var orStr = "";                             //!!!!!!!!!!!!!!!!!
            if(list[0].innerHTML != "") {
                if (query != "")  query += (" AND (");
                else query += "(";
                var listHtml = list[0].innerHTML;
                list.children("li").each(function() {
                    liStr = $(this)[0].innerHTML;
                    //If li is "none" change back to null
                    //liStr = liStr.replace("None","NULL");
                    if (filter_name == "Name") {
                        var n = liStr.split(" ,  ");
                        var ln = n[0];
                        var fn = n[1];
                        ln = ln.replace("'", "''");
                        fn = fn.replace("'", "''");
                        orStr += ("("+filter_type+".[FirstName]='"+fn+"'");
                        orStr += (" AND "+filter_type+".[LastName]='"+ln+"')");
                    } else {
                        if (liStr == "None")
                            orStr += (filter_type+".["+filter_name +"] IS NULL");
                        else
                            orStr += (filter_type+".["+filter_name +"]='"+liStr+"'");        //!!!!!!!!!!!!!!!!!!!!!
                    }
                    //Remove each li once appended in the Or string
                    $(this).remove();
                    //if empty then do NOT append Or, only append or if there are more li
                    if(list[0].innerHTML != "") {
                        orStr += (" OR ");           //!!!!!!!!!!!!!!!!!!!!!
                    }
                });
                list.append(listHtml);  //reappend original list
                orStr += ")";
            }
            query += orStr;
        });

        if(query=="") {
            // alert("No filters selected");
            return;
        }  else {
            query = " AND " + query;
        }

        // alert("Now the SQL Query is :\n" +query);
        $.ajax({
            url:"../lib/php/admin/applyFilters.php",
            type: "POST",
            dataType: "json",
            data: {
                query:query,
                current_fiscal_year:current_fiscal_year,
            },
            success:function(results){
                alert("Result size: " + results.length);
                $("#tableSizeSpan")[0].innerHTML = results.length;
                countAndSetSelected();
                loadReportSelection();
                for (var i = 0; i < results.length; i ++) {
                    var name = results[i]['FirstName']+" "+results[i]['LastName'];
                    //var audit = results[i]['Auditor'];
                    var certNo = results[i]['CertNo'];
                    var empNo = results[i]['EmployeeID'];
                    var balance = results[i]['CarryForwardTotal'];
                    loadTable(name,certNo,empNo,balance);
                    // var trHTML = "<tr>\
                    //                 <td><input type='checkbox' name='selected'></td>\
                    //                 <td class='nameinfo'>"+name+"</td>\
                    //                 <td class='emailInfo'>"+audit+"</td>\
                    //                 <td class='certNoInfo'>"+certNo+"</td>\
                    //                 <td><button class='viewReportBtn'><i class='fa fa-eye' aria-hidden='true'> View</i></button></td>\
                    //             </tr>";
                    /*
                	var parent = $("iframe#pdfBox").parent();
                    var newElement = "<iframe id='pdfBox' src='"+"../lib/php/usr/Report_userSpecificYear.php"+"' width='100%' height='800px'></iframe>";
                    $("iframe#pdfBox").remove();
                    parent.append(newElement);
                    $("#overviewTable tbody").append(trHTML);
					*/

                    // var markup = "<tr><td><input type='checkbox' name='selected'></td><td>" + name + "</td><td>" + audit + "</td></tr>";
                    // $("#overviewTable tbody").append(markup);
                }
                // alert("size of returned results is "+results.length);
                //console.log(escape(results));
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to filter");
                alert(status + error + xhr);
            },
            async:false
        });
    }

    function clearTable() {
        var row = 0;
        $("#overviewTable").find("tbody").find("tr").each(function() {
            if($(this).attr('id')=="overviewSelectAll") {}
            else{$(this).remove();}
        });
        $("#selectedSizeSpan")[0].innerHTML = 0;
    }

    function countAndSetSelected() {
        var count = 0;
        $("#overviewTable tbody").find("input").each(function() {
            if($(this).is(":checked")) {
                count += 1;
            }
        });
        $("#selectedSizeSpan")[0].innerHTML = count;
    }

    $(document).on("click",".viewReportBtn", function() {
        // TO DO: Fill in data
        var certNo = $(this).closest("tr").find(".certNoInfo")[0].innerHTML;
        var empNo = $(this).closest("tr").find(".empNoInfo")[0].innerHTML;
        alert("certno is "+certNo+ " EmpNo is "+empNo);
        var report_name = $("#reportTypeSelect option:selected").val();
        var report_file_name = getReportFileName(report_name);
        // var year_type = 1;  // # of year inputs

        var year1 = 0;
        var year2 = 0;

        //alert($(this).closest("tr").find(".specificYear").val());
        if(dropDownType==1) {
            year1 = parseInt($(this).closest("tr").find(".specificYear").val());//$("#reportTypeSelect option:selected").val();
        }
        else if(dropDownType==2) {
            year1 = parseInt($(this).closest("tr").find(".fromYear").val());
            year2 = parseInt($(this).closest("tr").find(".toYear").val());
            //Year validation
            if(year1>year2) {
                alert("Invalid range!");
                return;
            }
        }

        alert("click view " + certNo + " 's "+ " year1:"+year1+" year2:"+year2);
        $.ajax({
            url:"../lib/php/admin/reportCommunicator.php",
            type: "POST",
            data: {
                certNo:certNo,
                empNo:empNo,
                year_type:dropDownType,
                year1:year1,
                year2:year2,
                report_file_name:report_file_name,
            },
            success:function(result){
                if (result != "!UNDEFINED") {
                    var parent = $("#pdfBox").parent();
                    var newElement = "<object id='pdfBox' data='"+"../lib/php/admin/report/"+report_file_name+"' type='/pdf' width='100%' height='600px'>\
                                <embed src='"+"../lib/php/admin/report/"+report_file_name+"' type='application/pdf'></embed>\
                            </object>";
                    $("#pdfBox").remove();
                    parent.append(newElement);
                }
                else {
                    alert("Undefined Report Type!");
                }

            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when generaeting the report");
            }
        });

        $("#downloadCurrent").show();
    });


    //Filter Input change filters subset of dropdown options
    $(".leftInput input").on("change keyup paste click", function(){
      input = $(this);
      filterText = input.val().toUpperCase();
      filterTable = $(this).closest(".tableWrap").find(".filterContTable");

      filterTable.children("tbody").children("tr").each(function() {
          cols = $(this).children("td").eq(1);
          cols.each(function() {
            if ($(this)[0].innerHTML.toUpperCase().indexOf(filterText) > -1) {
              // $(this).parent().show();
              $(this).closest("tr").show();
            } else {
              // $(this).parent().hide();
              $(this).closest("tr").hide();
            }
          });
      });
    });

/*--------------------------------------------------------------------------------------------*/
/*------------------------------Display Accordions--------------------------------------------*/
/*--------------------------------------------------------------------------------------------*/

    // $.fn.togglepanels = function(){
    //   return this.each(function(){
    //     $(this).addClass("ui-accordion ui-accordion-icons ui-widget ui-helper-reset")
    //   .find("p")
    //     .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom")
    //     .hover(function() {
    //       $(this).toggleClass("ui-state-hover");
    //     })
    //     .prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>')
    //     .click(function() {
    //       $(this)
    //         .toggleClass("ui-accordion-header-active ui-state-active ui-state-default ui-corner-bottom")
    //         .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e ui-icon-triangle-1-s").end()
    //         .next().slideToggle();
    //       return false;
    //     })
    //     .next()
    //       .addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom")
    //       .hide();
    //   });
    // };

    // $("#accordion").togglepanels();

/*--------------------------------------------------------------------------------------------*/
/*------------------------------Display Accordions END----------------------------------------*/
/*--------------------------------------------------------------------------------------------*/


    // $("#filterApplyBtn").click(function(){
    // for (var i = 0; i < 3; i++) {
    //     var name = "NelsonLin" + i;
    //     var email = "nelsons@email.com";
    //     var markup = "<tr><td><input type='checkbox' name='selected'></td><td>" + name + "</td><td>" + email + "</td>\
    //                 <td></td></tr>";
    //     $("#overviewTable tbody").append(markup);
    //   }
    // });

    // Find and remove selected table rows
    $("#MyReport").click(function(){
        $("#overviewTable tbody").find('input[name="selected"]').each(function(){
          if($(this).is(":checked")){
                $(this).parents("tr").remove();
            }
        });
    });

    //Hiding subset of filters from result of changing result type selection
    // $( "#reportTypeSelect" ).change(function () {
    //     $(".dropDownFilter").show();
    //     $(".pFiltersLabel").show();
    //     var str = "";
    //     $( "#reportTypeSelect option:selected" ).each(function() {
    //         str += $( this ).text();
    //     });
    //     if (str.toUpperCase()=="Type1".toUpperCase()) {
    //         $("#employeeID_div").hide();
    //     }
    //     else if (str.toUpperCase()=="Type2".toUpperCase()) {
    //         $("#firstName_div").hide();
    //         $("#lastName_div").hide();
    //     }
    //     else if (str.toUpperCase()=="Type3".toUpperCase()) {
    //         $("#employeeFiltersLabel").hide();
    //         $(".employeeFilters").hide();
    //     }
    //     else {

    //     }
    // }).change();


    function checkTypeForDownload() {
        // alert("checkdownload");
        var str = $("#reportTypeSelect option:selected").text();
        // alert(str);
        if(str.toUpperCase()!="Annual Training Reports".toUpperCase()) {
            $("#downloadSelected").hide();
        }
    }

    $("#reportTypeSelect").on("change",function() {
        applyFilter();
        // $( "#reportTypeSelect option:selected" ).each(function() {
        //     str = $( this ).text();
        // });
        var str = $("#reportTypeSelect option:selected").text();

        if(str.toUpperCase()!="Annual Training Reports".toUpperCase()) {
            $("#downloadSelected").hide();
        }
        else {
            $("#downloadSelected").show();
        }
        // alert(str);
        for(var i=0;i<report_info.length;i++) {
            if(str.toUpperCase()==report_info[i][0].toUpperCase()) {
                $("#reportType").find(".toolTip")[0].innerHTML = report_info[i][3];
            }
        }
    });

    /*Filters: on input checkbox checked, add to right side list
    			on uncheck, remove from right side list*/

    $(".filterContTable").on("click", "input[name='selected']",function() {
        // var resultList = $(this).parent().parent().parent().parent().parent().next().children("ul");
        var tbody = $(this).parent().parent().parent();
        var resultList = $(this).closest(".tableWrap").next().children("ul");

        // var selectedVal = $(this).parent().next()[0].innerHTML;
        // if($(this).is(":checked")){
        //   // $("#homeTab").text($(this).parent().parent().parent().parent().prop("tagName"));
        //   var markup = "<li>" + selectedVal + "</li>";
        //   resultList.append(markup);
        // }
        // else {
        //   resultList.children("li").each(function() {
        //     // $("#homeTab").text($(this)[0].innerHTML.toUpperCase());
        //     if($(this)[0].innerHTML==selectedVal) {
        //       // $("#homeTab").text("reachremove");
        //       $(this).remove();
        //     }
        //   });
        // }

        resultList.children("li").each(function() {
	        $(this).remove();
	    });
        tbody.children("tr").each(function() {
        	if($(this).children("td").eq(0).children("input[name='selected']").is(":checked")) {
        		var contTd = $(this).children("td").eq(1);
        		var toAppend = contTd[0].innerHTML;
        		var markup = "<li>" + toAppend + "</li>";
        		resultList.append(markup);
        	}
        });


    });

    $("#overviewTable").on("click", "input[name='tableSelectAll']",function() {
        // $("#homeTab").text($(this).prop("tagName"));
        var tbody = $(this).closest("thead").next("tbody");
        // var selectAllLabel = $(this).parent().next();
        if($(this).is(":checked")){
            // selectAllLabel[0].innerHTML = "Deselect All";
            tbody.find('input[name="selected"]' && 'input:visible').each(function(){
                $(this).prop("checked",true);
            });
        }
        else {
            // selectAllLabel[0].innerHTML = "Select All";
            tbody.find('input[name="selected"]' && 'input:visible').each(function(){
                $(this).prop("checked",false);
            });
        }
    });

    $("#overviewTable").on("click", "input[name='selected'], input[name='tableSelectAll']", function() {
        countAndSetSelected();
    });


    /*Select All functionality*/
    $(".filterContTable").on("click", "input[name='selectAll']",function() {
    	// var resultList = $(this).parent().parent().parent().parent().parent().next().children("ul");
    	var resultList = $(this).closest(".tableWrap").next().children("ul");
    	// $("#homeTab").text(resultList.prop("tagName"));
    	// var tbody = $(this).parent().parent().parent().next("tbody");
    	var tbody = $(this).closest(".filterContTable").find("tbody");
    	var selectAllLabel = $(this).parent().next();
    	if($(this).is(":checked")){
            selectAllLabel[0].innerHTML = "Deselect All";
    		tbody.find('input[name="selected"]' && 'input:visible').each(function(){
	        	$(this).prop("checked",true);
	        });
        }
        else {
            selectAllLabel[0].innerHTML = "Select All";
        	tbody.find('input[name="selected"]' && 'input:visible').each(function(){
	        	$(this).prop("checked",false);
	        });
        }

        resultList.children("li").each(function() {
	        $(this).remove();
	    });
        tbody.children("tr").each(function() {
        	if($(this).children("td").eq(0).children("input[name='selected']").is(":checked")) {
        		var contTd = $(this).children("td").eq(1);
        		var toAppend = contTd[0].innerHTML;
        		var markup = "<li>" + toAppend + "</li>";
        		resultList.append(markup);
        	}
        });

    });


    /*Clickable dropdown*/
    $(".dropDownBtn").on("click", function(){
        var clicked = false;
        if($(this).data("clicked")) {
            clicked = true;
        }
        $(".DPBCont").each(function() {
            $(this).hide();
            $(this).parent().children(".dropDownBtn").data("clicked",false);
            $(this).parent().children(".dropDownBtn").find("i").remove();
            $(this).parent().children(".dropDownBtn").find("span").append("<i class='fa fa-angle-down' aria-hidden='true'></i>");
        });

        $(this).data("clicked",clicked);

    	if($(this).data("clicked")) {
    		$(this).data("clicked",false);
    		$(this).next().hide();
            $(this).parent().children(".dropDownBtn").find("i").remove();
            $(this).parent().children(".dropDownBtn").find("span").append("<i class='fa fa-angle-down' aria-hidden='true'></i>");
    	}
    	else {
	    	$(this).data("clicked",true);
	    	$(this).next().show();
            $(this).parent().children(".dropDownBtn").find("i").remove();
            $(this).parent().children(".dropDownBtn").find("span").append("<i class='fa fa-angle-up' aria-hidden='true'></i>");

    	}
    });


    // $(".dropDownFilter").focusout(function(){
    //     $(this).find(".dropDownBtn").data("clicked",false);
    //     $(this).find(".DPBCont").hide();
    // });

    // $(".dropDownFilter").focusin(function() {
    //     $(this).find(".dropDownBtn").data("clicked",true);
    //     $(this).find(".DPBCont").show();
    // });





    //Dropdown changes color on selected options
    $(".filterContTable").on("click", "input[name='selected'], input[name='selectAll']",function() {
        var DPBcont = $(this).closest(".DPBCont");
        var ul = DPBcont.find(".filterDisplayList").find("ul");
        var button = DPBcont.prev();
        if(ul[0].innerHTML == "") {
            button.css("background-color","white");
        }
        else {
            button.css("background-color","rgb(189, 207, 237)");
        }
    });


    /*Collapse will cause all dropdowns to hide*/
    $(".pFiltersLabel").on("click", function() {
    	$(this).next().find(".DPBCont").each(function() {
            $(this).hide();
            $(this).parent().children(".dropDownBtn").data("clicked",false);
        });
    });


    $("#emailDiv").hide();
    $("#EmailAll").on("click", function() {
        if($(this).data("clicked")) {
            $(this).data("clicked",false);
            $("#emailDiv").hide();
            $("#EmailAll").css( {
                "border-bottom-left-radius": "5px",
                "border-bottom-right-radius": "5px",
                "border-bottom-width": "1px",
                "border-style": "solid",
                "border-color": "white"
            });
        }
        else {
            $(this).data("clicked",true);
            $("#emailDiv").show();
            $("#EmailAll").css( {
                "border-bottom-left-radius": "0",
                "border-bottom-right-radius": "0",
                "border-bottom": "none"
            });
        }

    });

    function getEmailContent() {
        return $("#emailContentTA").val();
    }

    function getEmailSubject() {
    	return $("#emailSubjectTA").val();
    }

    $("#sendEmailSelectedBtn").on("click", function() {
        var numSelected = 0;
         $("#overviewTable").find(".infoInfo").each(function() {
            var checkbox = $(this).closest("tr").find("input[name='selected']");
            if(checkbox.is(":checked")) {
                numSelected+=1;
            }
        });
        if(confirm("Send emails to the selected "+numSelected+" people?")==0) {
            alert("pressed cancel");
            return;
        }
    	var subject = getEmailSubject();
        var content = getEmailContent();
        $("#overviewTable").find(".emailInfo").each(function() {
            var checkbox = $(this).closest("tr").find("input[name='selected']");
            if(checkbox.is(":checked")) {
                var address = $(this)[0].innerHTML;
                alert(address);
                // alert(stringEMAIL);
                $.ajax({
                    url:"../lib/php/admin/admin_email.php",
                    type: "POST",
                    data: {
                        address:address,
                        subject:subject,
                        content:content
                    },
                    success:function(results){
                        if (results == "success")
                            alert("Email Sent!");
                        else
                            alert("Error when sending email");
                    },
                    error: function(xhr, status, error){
                        alert("Fail to connect to server when sending email.");
                    }
                });
            }
        });


    });

    // $("#filterApplyBtn").click(applyFilter);
    $("#filterApplyBtn").on("click", function() {
        $(this).find("button")[0].innerHTML = "<i class='fa fa-spinner fa-pulse fa-3x fa-fw'></i> Loading...";
        setTimeout(function(){
            applyFilter();
            $("#filterApplyBtn").find("button")[0].innerHTML = "Apply Filter";
        },4000);
        // applyFilter();
        // $(this).children()[0].innerHTML = "Apply Filter";
    });


    function setSegActive(seg) {
        $("#buttonTabDiv").find("button").each(function() {
            $(this).css({
                "background-color":"rgba(255,255,255,0)",
                "color":"white"
            });
        });
        if(seg==0) {
            $("#Download").css({
                "background-color":"rgba(255,255,255,1)",
                "color":"black"
            });
        }
        else {
            $("#EmailAll").css({
                "background-color":"rgba(255,255,255,1)",
                "color":"black"
            });
        }
    }


    $(".resetBtn").on("click",function() {
        $(this).closest(".DPBCont").find("input[name='selected'], input[name='selectAll']").each(function() {
            $(this).prop("checked",false);
        });

        $(this).closest(".DPBCont").find("input[name='selectAll']").closest("td").next("td")[0].innerHTML = "Select All";

        $(this).closest(".DPBCont").find("li").each(function() {
            $(this).remove();
        });

        $(this).closest(".dropDownFilter").find(".dropDownBtn").css("background-color","white");
    });

    $("#resetAllBtn").on("click",function() {
        var filterList = $(this).next(".filterListCol");
        filterList.find("input[name='selected'], input[name='selectAll']").each(function() {
            $(this).prop("checked",false);
        });

        filterList.find("input[name='selectAll']").each(function() {
            $(this).closest("td").next("td")[0].innerHTML = "Select All";
        });

        filterList.find("li").each(function() {
            $(this).remove();
        });

        filterList.find(".dropDownBtn").css("background-color","white");
    });



    $(".toolTipParent").hover(function() {
        $(this).find(".toolTip").show();
    }, function() {
        $(this).find(".toolTip").hide();
    });

    $("#downloadCurrent").hide();

    // $("#downloadSelected").on("click",function() {
    //     var idArray = [];
    //     $("#overviewTable").find(".certNoInfo").each(function() {
    //         idArray.push($(this)[0].innerHTML);
    //     });
    // });

    $("#hoursShortDiv").find("input").each(function() {
        $(this).prop("checked",true);
    });

    $("#hoursShortDiv input[name='zero']").on("click", function() {
        // alert("11");
        var checked = false;
        if($(this).is(":checked")){
            checked = true;
        }
        filterRange(0,0,checked);
    });

    $("#hoursShortDiv input[name='one']").on("click", function() {
        // alert("11");
        var checked = false;
        if($(this).is(":checked")){
            checked = true;
        }
        filterRange(1,5,checked);
    });

    $("#hoursShortDiv input[name='six']").on("click", function() {
        // alert("11");
        var checked = false;
        if($(this).is(":checked")){
            checked = true;
        }
        filterRange(6,10,checked);
    });

    $("#hoursShortDiv input[name='eleven']").on("click", function() {
        // alert("11");
        var checked = false;
        if($(this).is(":checked")){
            checked = true;
        }
        filterRange(11,15,checked);
    });

    $("#hoursShortDiv input[name='sixteen']").on("click", function() {
        // alert("11");
        var checked = false;
        if($(this).is(":checked")){
            checked = true;
        }
        filterRange(16,999,checked);
    });

    function filterRange(start,end,show) {
        $("#overviewTable").find(".hoursShortInfo").each(function() {
            var hours = parseInt($(this)[0].innerHTML);
            if(hours>=start && hours<=end) {
                if(show) {
                    $(this).closest("tr").show();
                }
                else {
                    // $(this).closest("tr").find("input").prop("checked",false);
                    $(this).closest("tr").hide();
                }
            }
        });
    }


    function sortTable(f,n){
        var rows = $('#overviewTable tbody  tr').get();

        rows.sort(function(a, b) {
            var A = getVal(a);
            var B = getVal(b);

            if(A > B) {
                return -1*f;
            }
            if(A < B) {
                return 1*f;
            }
            return 0;
        });

        function getVal(elm){
            var v = $(elm).children('td').eq(n).text().toUpperCase();
            if($.isNumeric(v)){
                v = parseInt(v,10);
            }
            return v;
        }

        $.each(rows, function(index, row) {
            $('#overviewTable').children('tbody').append(row);
        });
    }

    var f_name = 1;
    var f_hour = 1;

    $("#nameSort").on("click",function() {
        var numCol = $(this).closest("tr").children().index($(this).closest("th"));
        f_name *= -1;
        sortTable(f_name,numCol);
    });

    $("#hoursSort").on("click",function() {
        var numCol = $(this).closest("tr").children().index($(this).closest("th"));
        f_hour *= -1;
        sortTable(f_hour,numCol);
    });

    //Make the overviewTable rows draggable, NOT supported in IE! hence commented out
    //$("#overviewTable tbody").sortable();

});


