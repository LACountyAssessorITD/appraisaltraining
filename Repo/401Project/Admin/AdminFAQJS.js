$(document).ready(function(){

  $("#faqTab").on("click",function(e) {
      e.preventDefault();
  });

  // $(function(){
  //       $.datepicker.setDefaults(
  //         $.extend( $.datepicker.regional[ '' ] )
  //       );
  //       $( '#datepicker' ).datepicker();
  //     });


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

});
