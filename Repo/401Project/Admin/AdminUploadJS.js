$(document).ready(function(){

  $("#updateTab").on("click",function(e) {
      e.preventDefault();
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

    function hideAllSeg() {
        $(".upload").hide();
        $(".restore").hide();
        $(".xrefDiv").hide()
    }

    hideAllSeg();
    $(".upload").show();
    setSegActive(0);
    $("#uploadSegCtrl").on("click", function() {
        hideAllSeg();
        $(".upload").show();
        // $(".restore").hide();
        setSegActive(0);
        // $(".upload").show("slide",{direction: "left"});
        // $(".restore").hide("slide",{direction: "right"});
    });

    $("#restoreSegCtrl").on("click", function() {
        hideAllSeg();
        // $(".upload").hide();
        $(".restore").show();
        setSegActive(1);
        // $("#restoreSegCtrl").css("color","black");
     //   $(".upload").hide("slide", {direction: "left"});
     //   $(".restore").show("slide", {direction: "right"});
    });

    $("#xrefSegCtrl").on("click", function() {
        hideAllSeg();
        // $(".upload").hide();
        $(".xrefDiv").show();
        setSegActive(2);
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
        $("#restoreSegCtrl, #xrefSegCtrl").css({
            "background-color":"rgba(255,255,255,0",
            "color":"white"
          });
      }
      else if(seg==1){
        $("#restoreSegCtrl").css({
            "background-color":"rgba(255,255,255,1",
            "color":"black"
          });
        $("#uploadSegCtrl, #xrefSegCtrl").css({
            "background-color":"rgba(255,255,255,0",
            "color":"white"
          });
      }
      else {
        $("#xrefSegCtrl").css({
            "background-color":"rgba(255,255,255,1",
            "color":"black"
          });
        $("#restoreSegCtrl, #uploadSegCtrl").css({
            "background-color":"rgba(255,255,255,0",
            "color":"white"
          });
      }
    }

    $("#chooseFileBtn").on("click",function() {
      $("#fileToUpload").click();
    });

    $(document).on("change","#fileToUpload",function() {
      $("#chosenFileName").text($("#fileToUpload").val());
    });

    $("#submitNewBtn").on("click",function() {
      // $("#chosenFileName").text("");
    });

    //Expand or collapse edit xrefDiv
    $(document).on("click",".editRowBtn", function() {
    // $(".editRowBtn").on("click", function() {
      if($(this).data("clicked")) {
        $(this).data("clicked",false);
        $(this).next(".editRowDiv").hide();
      }
      else {
        $(this).data("clicked",true);
        $(this).next(".editRowDiv").show();
      }
    });

    //Confirm update of row
    $(document).on("click",".confirmEditBtn", function() {
    // $(".confirmEditBtn").on("click",function() {
      var employeeIDStr = $(this).closest("div").find("input[name='EmployeeIDInput']").val();
      var certNoStr = $(this).closest("div").find("input[name='CertNoInput']").val();
      if(confirm("Update Employee ID with "+employeeIDStr+" and CertNo with "+certNoStr+"?")==0) {
        alert("pressed cancel");
        return;
      }
      $(this).closest("tr").find(".EmployeeIDData")[0].innerHTML = employeeIDStr;
      $(this).closest("tr").find(".CertNoData")[0].innerHTML = certNoStr;
      $(this).closest("td").find(".editRowBtn").data("clicked",false);
      $(this).closest(".editRowDiv").hide();
    });

    //Delete Row
    $(document).on("click",".deleteRowBtn", function() {
    // $(".deleteRowBtn").on("click", function() {
      if(confirm("Delete this row?")==0) {
        alert("pressed cancel");
        return;
      }
      $(this).closest("tr").remove();
    });

    //Insert Row
    $(".insertRowBtn").on("click",function() {
      var employeeIDNew = $(this).closest("#insertNewRowDiv").find("input[name='InsertEmployeeIDInput']").val();
      var certNoNew = $(this).closest("#insertNewRowDiv").find("input[name='InsertCertNoInput']").val();

      if(confirm("Insert this new row with Employee ID: "+employeeIDNew+" and CertNo: "+certNoNew+"?")==0) {
        alert("pressed cancel");
        return;
      }

      insertRow(employeeIDNew, certNoNew);

      $(this).closest("#insertNewRowDiv").find("input[name='InsertEmployeeIDInput']").val("");
      $(this).closest("#insertNewRowDiv").find("input[name='InsertCertNoInput']").val("");

    });



    insertRow(121212, 232323);
    insertRow(342342, 132435);

    function insertRow(employeeIDNew, certNoNew) {
      var markup = "<tr>\
                      <td class='EmployeeIDData'>"+employeeIDNew+"</td>\
                      <td class='CertNoData'>"+certNoNew+"</td>\
                      <td>\
                        <button class='editRowBtn'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button>\
                        <div class='editRowDiv'>\
                          <label>EmployeeID</label>\
                          <input type='text' name='EmployeeIDInput' value='"+employeeIDNew+"'>\
                          <br>\
                          <label>CertNo</label>\
                          <input type='text' name='CertNoInput' value='"+certNoNew+"'>\
                          <br>\
                          <button class='confirmEditBtn'>Confirm Edit</button>\
                        </div>\
                      </td>\
                      <td><button class='deleteRowBtn'><i class='fa fa-times' aria-hidden='true'></i></button></td>\
                    </tr>";
      $("#xrefTable").append(markup);
    }

    //Numerical input check
    $("input[name='EmployeeIDInput'], input[name='CertNoInput'],input[name='InsertEmployeeIDInput'],\
      input[name='InsertCertNoInput']").on("keypress keyup blur",function (event) {    
       $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

});