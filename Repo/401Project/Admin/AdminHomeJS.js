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

    function loadFilterOptions() {
        $(".dropDownFilter").each(function() {
            // $("#homeTab").text($(this).children().prop("tagName"));
            var DPBContHtml = "<div class='DPBCont'>\
                                    <div class='tableWrap'>\
                                        <form class='leftInput'>\
                                            <input type='text' placeholder='Search..'' autocomplete='off'></form>\
                                    	<div class='filterContTableBG'></div>\
                                        <table class='filterContTable'>\
                                            <col width='20'>\
                                            <thead>\
                                                <tr>\
                                                    <td><input type='checkbox' name='selectAll'></td>\
                                                    <td>Select All</td>\
                                                </tr>\
                                            </thead>\
                                            <tbody></tbody>\
                                        </table>\
                                    </div>\
                                    <div class='filterDisplayList'>\
                                        <label>Selections:</label>\
                                        <ul></ul>\
                                    </div>\
                                    <iframe class='cover' src='about:blank'></iframe>\
                                </div>";

            $(this).append(DPBContHtml);
        });
    }

    loadTable("nelson", "yuehhsul@usc.edu");

    function loadTable(name, email) {
        var trHTML = "<tr>\
                            <td><input type='checkbox' name='selected'></td>\
                            <td>"+name+"</td>\
                            <td>"+email+"</td>\
                            <td><button class='viewReportBtn'></button></td>\
                        </tr>";

        $("#overviewTable tbody").append(trHTML);
    }



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


    $("#filterApplyBtn").click(function(){
    for (var i = 0; i < 3; i++) {
        var name = "NelsonLin" + i;
        var email = "nelsons@email.com";
        var markup = "<tr><td><input type='checkbox' name='selected'></td><td>" + name + "</td><td>" + email + "</td></tr>";
        $("#overviewTable tbody").append(markup);
      }
    });

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


    /*Select All functionality*/
    $(".filterContTable").on("click", "input[name='selectAll']",function() {
    	// var resultList = $(this).parent().parent().parent().parent().parent().next().children("ul");
    	var resultList = $(this).closest(".tableWrap").next().children("ul");
    	// $("#homeTab").text(resultList.prop("tagName"));
    	// var tbody = $(this).parent().parent().parent().next("tbody");
    	var tbody = $(this).closest(".filterContTable").find("tbody");
    	// $("#homeTab").text(table.prop("tagName"));
    	if($(this).is(":checked")){
    		tbody.find('input[name="selected"]' && 'input:visible').each(function(){
	        	$(this).prop("checked",true);
	        });
        }
        else {
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

    $("#sendEmailBtn").on("click", function() {
    	var subject = getEmailSubject();
        var content = getEmailContent();
        $.ajax({
            url:"../lib/php/admin/admin_email.php",
            type: "POST",
            data: {
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

    });

});


