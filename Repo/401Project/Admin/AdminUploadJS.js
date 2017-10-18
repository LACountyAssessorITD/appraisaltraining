$(document).ready(function(){

  // location.reload();

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

    // $("#accordion").togglepanels();

/*--------------------------------------------------------------------------------------------*/
/*------------------------------Display Accordions END----------------------------------------*/
/*--------------------------------------------------------------------------------------------*/


    $("#uploadForm").each(function() {
    	$(this)[0].reset();
    });

    //  $("#uploadForm").submit(function() {
    //     submitForm();
    //     return false;
    // });

    // function submitForm() {
    //     $.ajax({url:"AdminUploadPHP.php"});
    // }
    $(".restore").hide();
    setSegActive(0);
    $("#uploadSegCtrl").on("click", function() {
        $(".upload").show();
        $(".restore").hide();
        setSegActive(0);
        // $(".upload").show("slide",{direction: "left"});
        // $(".restore").hide("slide",{direction: "right"});
    });

    $("#restoreSegCtrl").on("click", function() {
        $(".upload").hide();
        $(".restore").show();
        setSegActive(1);
        // $("#restoreSegCtrl").css("color","black");
     //   $(".upload").hide("slide", {direction: "left"});
     //   $(".restore").show("slide", {direction: "right"});
    });

    function setSegActive(seg) {
      if(seg==0) {
        $("#uploadSegCtrl").css({
            "background-color":"rgba(255,255,255,1",
            "color":"black"
          });
        $("#restoreSegCtrl").css({
            "background-color":"rgba(255,255,255,0",
            "color":"white"
          });
      }
      else {
        $("#uploadSegCtrl").css({
            "background-color":"rgba(255,255,255,0",
            "color":"white"
          });
        $("#restoreSegCtrl").css({
            "background-color":"rgba(255,255,255,1",
            "color":"black"
          });
      }
    }

    $("#submitNewBtn").on("click",function() {
      $("#chosenFile").val($("#fileToUpload").val());
    });

    $("#chooseFileBtn").on("click",function() {
      $("#fileToUpload").click();
    })

});