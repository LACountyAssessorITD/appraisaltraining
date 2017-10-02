function myFunction(inputId, tableId) {
  // Declare variables 
  var input, filter, table, tr, td, i;
  input = document.getElementById(inputId);
  filter = input.value.toUpperCase();
  table = document.getElementById(tableId);
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
  }
}


$(document).ready(function(){

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

});


