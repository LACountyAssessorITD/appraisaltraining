$(document).ready(function(){

  $(".leftInput input").on("change keyup paste click", function(){
      input = $(this);
      filterText = input.val().toUpperCase();
      filterTable = $(this).parent().next().children("table");

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

    $(".filterContTable").on("click", "input[type='checkbox']",function() {
        var resultList = $(this).parent().parent().parent().parent().parent().next().children("ul");
        var selectedVal = $(this).parent().next()[0].innerHTML;
        if($(this).is(":checked")){
          // $("#homeTab").text($(this).parent().parent().parent().parent().prop("tagName"));
          var markup = "<li>" + selectedVal + "</li>";
          resultList.append(markup);
        }
        else {
          resultList.children("li").each(function() {
            // $("#homeTab").text($(this)[0].innerHTML.toUpperCase());
            if($(this)[0].innerHTML==selectedVal) {
              // $("#homeTab").text("reachremove");
              $(this).remove();
            }
          });
        }
    });

    $(".dropDownBtn").on("click", function(){
    	if($(this).data("clicked")) {
    		$(this).data("clicked",false);
    		$(this).next().hide();
    	}
    	else {
	    	$(this).data("clicked",true);
	    	$(this).next().show();
    	}
    });

});


