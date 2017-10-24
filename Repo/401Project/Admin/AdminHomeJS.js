$(document).ready(function(){
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
                for (var i = 2; i < size; i++) {
	                var type = results[i];
	                type = type.replace(".php","");
	                temp += "<option>"+type+"</option>";
                }
                // update drop down selections - reportTypeSelect
                var parent = $("select#reportTypeSelect").parent();
                var newElement = '<select id="reportTypeSelect">'+temp+'</select>';
                $("select#reportTypeSelect").remove();
                parent.append(newElement);
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to retrieve report types");
            }
        });
    }

    loadFilterOptions();

    function count() {
        $("#homeTab").text("hello");
    }

    function loadFilterOptions() {
        $(".dropDownFilter").each(function() {
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
                htmlStr += "<tr><td><input type='checkbox' name='selected'></td>\
                            <td>"+result_array[i]+"</td></tr>";
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
    }

    // function appendHTMLFilterDropDown(thisObj,result_array) {
    //     var DPBContHtml_top = "<div class='DPBCont'>\
    //                             <div class='tableWrap'>\
    //                                 <form class='leftInput'>\
    //                                     <input type='text' placeholder='Search..'' autocomplete='off'></form>\
    //                                 <div class='filterContTableBG'></div>\
    //                                 <table class='filterContTable'>\
    //                                     <col width='20'>\
    //                                     <thead>\
    //                                         <tr>\
    //                                             <td><input type='checkbox' name='selectAll'></td>\
    //                                             <td>Select All</td>\
    //                                         </tr>\
    //                                     </thead>\
    //                                     <tbody>\
    //                                         <tr>";

    //     var DPBContHtml_bottom = "\
    //                                         </tr>\
    //                                     </tbody>\
    //                                 </table>\
    //                             </div>\
    //                             <div class='filterDisplayList'>\
    //                                 <label>Selections:</label>\
    //                                 <ul></ul>\
    //                             </div>\
    //                             <iframe class='cover' src='about:blank'></iframe>\
    //                         </div>";

    //     $(this).append(DPBContHtml_top);

    //     var i;
    //     for(i=0;i<result_array.length;i++) {
    //         var htmlStr = "<td><input type='checkbox' name='selected'></td>\
    //                     <td>"+result_array[i]+"</td>";
    //         $(this).append(htmlStr);
    //     }

    //     $(this).append(DPBContHtml_bottom);
    // }


    function getFilterNameAndType(filter_name, filter_type) {
        // send request to retrieve distinctive rows
         $.ajax({
            url:"../lib/php/admin/getFilters.php",
            type: "POST",
            dataType: "text",
            data: {
                filter_name:filter_name,
                filter_type:filter_type
            },
            success:function(results){
                return JSON.parse(results);
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when trying to retrieve filters");
                alert(status);
            }
        });
    }

    function loadTable(name, email, certNo) {
        var trHTML = "<tr>\
                        <td><input type='checkbox' name='selected'></td>\
                        <td class='nameinfo'>"+name+"</td>\
                        <td class='emailInfo'>"+email+"</td>\
                        <td class='certNoInfo'>"+certNo+"</td>\
                        <td><button class='viewReportBtn'><i class='fa fa-eye' aria-hidden='true'> View</i></button></td>\
                    </tr>";

        $("#overviewTable tbody").append(trHTML);
    }

    loadTable("nelson", "yuehhsul@usc.edu", "1234");
    loadTable("testAdmin", "assessortestpdf@gmail.com", "5678");
    loadTable("Yining", "yininghu@usc.edu", "91011");

    function applyFilter() {
        clearTable();
        // generate SQL query clause
        var query = "";                               //!!!!!!!!!!!!!!!!!!!
        var filterNum = 0;
        $(".dropDownFilter").each(function() {
            if (query != "")  query += (" AND "); 
            var filterDisplayList = $(this).find(".filterDisplayList");
            var filter_name = $(this).children(".dropDownBtn").attr("name");
            var list = filterDisplayList.children("ul");
            var orStr = "";                             //!!!!!!!!!!!!!!!!!
            if(list[0].innerHTML != "") {  // if user select any options in the dropdown
                var listHtml = list[0].innerHTML;
                list.children("li").each(function() {
                    liStr = $(this)[0].innerHTML;
                    orStr += (filter_name +"="+liStr);        //!!!!!!!!!!!!!!!!!!!!!
                    //Remove each li once appended in the Or string
                    $(this).remove();
                    //if empty then do NOT append Or, only append or if there are more li
                    if(list[0].innerHTML != "") {
                        orStr += (" OR ");           //!!!!!!!!!!!!!!!!!!!!!
                    }
                });
                list.append(listHtml);  //reappend original list
                // if(filterNum!=0) {
                //     query += (orStr+ " AND ");  //the rest in the query statement !!!!!!!!!!!!!!!!
                // }
                // else {
                //     query += (""+orStr+"");    //First in the query statement !!!!!!!!!!!!!!!!!!!!!
                // }
            }
        });

        alert("Now the SQL Query is :" +query);
        $.ajax({
                url:"../lib/php/admin/applyFilters.php",
                type: "POST",
                dataType: "json",
                data: {
                    query:query,
                },
                success:function(results){
                    for (var i = 0; i < results.length; i ++) {
                        var name = results[i]['FirstName']+" "+results[i]['LastName'];
                        var audit = results[i]['Auditor'];
                        var certNo = results[i]['CertNo'];
                        var email = "someEmail@email.com";
                        loadTable(name,email,certNo);
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
                    alert("size of returned results is "+results.length);
                    //console.log(escape(results));
                },
                error: function(xhr, status, error){
                    alert("Fail to connect to the server when trying to filter");
                    //alert(status + error + xhr);
                },
                async:false
            });
    }

    function clearTable() {
        var row = 0;
        $("#overviewTable").find("tr").each(function() {
            if($(this).attr('id')=="overviewSelectAll") {}
            else{$(this).remove();}
        });
    }

    $(document).on("click",".viewReportBtn", function() {

    // });
    // $(".viewReportBtn").on("click", function() {
        var certNo = $(this).closest("tr").find(".certNoInfo")[0].innerHTML;
        alert("click view " + certNo + " 's report");
        $.ajax({
            url:"../lib/php/admin/reportCommunicator.php",
            type: "POST",
            data: {certNo:certNo},
            success:function(result){
                var parent = $("#pdfBox").parent();
                var newElement = "<iframe id='pdfBox' src='"+"../lib/php/admin/reportType/1_Specific_Year.php"+"' frameborder='0' scrolling='auto' width='100%' height='800px'></iframe>";
                $("#pdfBox").remove();
                parent.append(newElement);
            },
            error: function(xhr, status, error){
                alert("Fail to connect to the server when generaeting the report");
            }
        });
    });


    $(".leftInput input").on("change keyup paste click", function(){
      input = $(this);
      filterText = input.val().toUpperCase();
      filterTable = $(this).parent().next().next("table");

      filterTable.children("tbody").children("tr").each(function() {
          cols = $(this).children("td").eq(1);
          cols.each(function() {
            if ($(this)[0].innerHTML.toUpperCase().indexOf(filterText) > -1) {
              $(this).parent().show();
            } else {
              $(this).parent().hide();
            }
          });
      });
    });

/*--------------------------------------------------------------------------------------------*/
/*------------------------------Display Accordions--------------------------------------------*/
/*--------------------------------------------------------------------------------------------*/

    $.fn.togglepanels = function(){
      return this.each(function(){
        $(this).addClass("ui-accordion ui-accordion-icons ui-widget ui-helper-reset")
      .find("p")
        .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom")
        .hover(function() {
          $(this).toggleClass("ui-state-hover");
        })
        .prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>')
        .click(function() {
          $(this)
            .toggleClass("ui-accordion-header-active ui-state-active ui-state-default ui-corner-bottom")
            .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e ui-icon-triangle-1-s").end()
            .next().slideToggle();
          return false;
        })
        .next()
          .addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom")
          .hide();
      });
    };

    $("#accordion").togglepanels();

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
    $( "#reportTypeSelect" ).change(function () {
        $(".dropDownFilter").show();
        $(".pFiltersLabel").show();
        var str = "";
        $( "#reportTypeSelect option:selected" ).each(function() {
            str += $( this ).text();
        });
        if (str.toUpperCase()=="Type1".toUpperCase()) {
            $("#employeeID_div").hide();
        }
        else if (str.toUpperCase()=="Type2".toUpperCase()) {
            $("#firstName_div").hide();
            $("#lastName_div").hide();
        }
        else if (str.toUpperCase()=="Type3".toUpperCase()) {
            $("#employeeFiltersLabel").hide();
            $(".employeeFilters").hide();
        }
        else {

        }
    }).change();

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
        var tbody = $(this).closest("tbody");
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
        });

        $(this).data("clicked",clicked);

    	if($(this).data("clicked")) {
    		$(this).data("clicked",false);
    		$(this).next().hide();
    	}
    	else {
	    	$(this).data("clicked",true);
	    	$(this).next().show();
    	}
    });


    //Dropdown changes color on selected options
    $(".filterContTable").on("click", "input[name='selected'], input[name='selectAll']",function() {
        var DPBcont = $(this).closest(".DPBCont");
        var ul = DPBcont.find(".filterDisplayList").find("ul");
        var button = DPBcont.prev();
        if(ul[0].innerHTML == "") {
            button.css("background-color","white");
        }
        else {
            button.css("background-color","blue");
        }
    });


    /*Collapse will cause all dropdowns to hide*/
    $(".pFiltersLabel").on("click", function() {
    	$(this).next().find(".DPBCont").each(function() {
            $(this).hide();
            $(this).parent().children(".dropDownBtn").data("clicked",false);
        });
    });


    //Email Alert

    $("#emailDiv").hide();
    $("#EmailAll").on("click", function() {
    	if($(this).data("clicked")) {
    		$(this).data("clicked",false);
    		$("#emailDiv").hide();
    	}
    	else {
	    	$(this).data("clicked",true);
	    	$("#emailDiv").show();
    	}
    });

    function getEmailContent() {
        return $("#emailContentTA").val();
    }

    function getEmailSubject() {
    	return $("#emailSubjectTA").val();
    }

    $("#sendEmailSelectedBtn").on("click", function() {
        if(confirm("Send email to the selected people?")==0) {
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
    $("#filterApplyBtn").on("click", applyFilter);
});


