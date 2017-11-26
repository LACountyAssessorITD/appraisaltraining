$(document).ready(function(){

    $("#updateTab").on("click",function(e) {
        e.preventDefault();
    });


    $("#effDateBtn").on("click",function() {
        // if($("#yearEffInput").val()=="" || $("#monthEffInput").val()=="" || $("#dayEffInput").val()=="") {
        //     alert("All date fields should be filled");
        // }
        // var year = parseInt($("#yearEffInput").val());
        // var month = parseInt($("#monthEffInput").val());
        // var day = parseInt($("#dayEffInput").val());

        var validDate = checkValidDate();

        if(validDate) {
            if(confirm("Update effective date to "+month+"/"+day+"/"+year+"?")) {
                alert("date updated");
            }
            else {
                alert("cancelled");
            }
        }

    });

    getCurrDate();

    function getCurrDate() {
      var currDate = new Date();
      var d = currDate.getDate();
      var m = currDate.getMonth()+1;
    //   (month<10 ? '0' : '') + month + '/' +
    // (day<10 ? '0' : '') + day;
    //   if(m>12) {
    //     m = 1;
    //   }
      var y = currDate.getFullYear();
      $("#yearEffInput").attr('value',y);
      $("#monthEffInput").attr('value',(m<10 ? '0' : '') + m);
      $("#dayEffInput").attr('value',(d<10 ? '0' : '') + d);
    }

    function checkValidDate(year, month, day) {
        if($("#yearEffInput").val()=="" || $("#monthEffInput").val()=="" || $("#dayEffInput").val()=="") {
            alert("All date fields should be filled");
        }
        var year = parseInt($("#yearEffInput").val());
        var month = parseInt($("#monthEffInput").val());
        var day = parseInt($("#dayEffInput").val());

        var date = new Date(year,month-1,day);
        if(date.getFullYear()==year && (date.getMonth()+1)==month && date.getDate()==day) {
            return true;
        }
        else {
            alert("invalid date");
            return false;
        }
    }

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

    $("#chooseFileBtn").on("click",function(evt) {
       evt.preventDefault();
      $("#fileToUpload").click();
    });

    $(document).on("change","#fileToUpload",function() {
      $("#chosenFileName").text($("#fileToUpload").val());
    });

    $("#effDateBtn").hide();

    $("#chooseFileBtn").on("click",function() {
      if($("#chosenFileName").val().toUpperCase()=="none".toUpperCase()) {
        $("#effDateBtn").hide();
      }
      else {
        $("#effDateBtn").show();
      }
    });

    $("#submitNewBtn").on("click",function() {
      // $("#chosenFileName").text("");
      // alert("clicked");
      if(!checkValidDate()) {
        return false;
      }
    });


    $("#uploadForm").submit(function(evt){
      // alert("Submitted!");
      evt.preventDefault();
      var formData = new FormData($(this)[0]);
      $.ajax({
        url: "../lib/php/admin/AdminUploadPHP.php",
        type: 'POST',
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        success: function (response) {
          if (response.indexOf("success") !== -1) { // if upload success
            alert("Files Successfully Uploaded!");
            if(confirm("Do you want to update the db file now?")){
              var dir = response.replace('success:','');
              alert("Start updating! @ " + dir);
            }
          }
          else {      // upload fail
            alert("Upload Failed! Error: " + response);
          }

        }
      });
      document.getElementById("uploadForm").reset();
      document.getElementById("chosenFileName").innerHTML = "None";
      return false;
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
      checkMismatch();
    });

    //Delete Row
    $(document).on("click",".deleteRowBtn", function() {
    // $(".deleteRowBtn").on("click", function() {
      if(confirm("Delete this row?")==0) {
        alert("pressed cancel");
        return;
      }
      $(this).closest("tr").remove();
      checkMismatch();
    });

    //Insert Row
    $(".insertRowBtn").on("click",function() {
      var employeeIDNew = $(this).closest("#insertNewRowDiv").find("input[name='InsertEmployeeIDInput']").val();
      var certNoNew = $(this).closest("#insertNewRowDiv").find("input[name='InsertCertNoInput']").val();

      if(employeeIDNew==""||certNoNew=="") {
        alert("Both fields should be filled.");
        return;
      }

      if(confirm("Insert this new row with Employee ID: "+employeeIDNew+" and CertNo: "+certNoNew+"?")==0) {
        alert("pressed cancel");
        return;
      }

      var fName = "";
      var lName = "";
      $.ajax({
          url:"../lib/php/admin/getXrefTable.php",
          type: "POST",
          dataType: "json",
          success:function(results){
              for (var i = 0 ; i < results.length; i ++) {
                if(results[i]['CertNo']==employeeIDNew) {
                  fName = results[i]['FirstName'];
                  lName = results[i]['LastName'];
                  break;
                }
              }
          },
          error: function(xhr, status, error){
              alert("Fail to connect to the server when trying to load xref table");
          },
          async: false
      });

      insertRow(employeeIDNew, certNoNew, fName, lName);

      checkMismatch();

      $(this).closest("#insertNewRowDiv").find("input[name='InsertEmployeeIDInput']").val("");
      $(this).closest("#insertNewRowDiv").find("input[name='InsertCertNoInput']").val("");

    });

    function loadTable() {
      $.ajax({
          url:"../lib/php/admin/getXrefTable.php",
          type: "POST",
          dataType: "json",
          success:function(results){
              for (var i = 0 ; i < results.length; i ++) {
                insertRow(results[i]['EmployeeID'],results[i]['CertNo'],results[i]['FirstName'],results[i]['LastName']);
              }
              checkMismatch();
          },
          error: function(xhr, status, error){
              alert("Fail to connect to the server when trying to load xref table");
          },
          async: false
      });
      // checkMismatch();
    }


    loadTable();  // To load Xref table for admin

    function insertRow(employeeIDNew, certNoNew, firstName, LastName) {
      //These two should be found based on employeeID and CertNO

      var empName = "name";
      // $.ajax({
      //       url:"../LDAP/getLdapInfo.php",
      //       type: "POST",
      //       dataType: "json",
      //       data: {
      //           empNo:empNo,
      //       },
      //       success:function(results){
      //           // alert(results[1]);
      //           empName = results[0];
      //       },
      //       error: function(xhr, status, error){
      //           // alert(empNo);
      //           // alert(error);
      //       },
      //       async:false
      //   });
      var certName = firstName + " " + LastName;

      var markup = "<tr>\
                      <td class='EmployeeIDData'>"+employeeIDNew+"</td>\
                      <td class='EmployeeIDName'>"+empName+"</td>\
                      <td class='CertNoData'>"+certNoNew+"</td>\
                      <td class='CertNoName'>"+certName+"</td>\
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


    $("#xrefSearchBar input").on("change keyup paste click", function(){
        input = $(this);
        filterText = input.val().toUpperCase();
        filterTable = $("#xrefTable");

        filterTable.children("tbody").children("tr").each(function() {
            var found = false;
            $(this).find("td").each(function() {
              if ($(this)[0].innerHTML.toUpperCase().indexOf(filterText) > -1) {
                // $(this).parent().show();
                found = true;
              }
            });

            if(found==false) {
              $(this).closest("tr").hide();
            }
            else {
              $(this).closest("tr").show();
            }
        });
    });

    function checkMismatch() {
        $("#mismatchList")[0].innerHTML = "";
        var numMismatch = 0;
        $("#xrefTable tbody").find("tr").each(function() {
            var empName = $(this).find(".EmployeeIDName")[0].innerHTML;
            var certName = $(this).find(".CertNoName")[0].innerHTML;
            // var empName = "name1";
            // var certName = "name2";
            if(empName==""||certName=="") {
              alert("emp or cert name is null");
              return;
            }
            if(empName.toUpperCase()!=certName.toUpperCase()) {
              var employeeIDNum = $(this).find(".EmployeeIDData")[0].innerHTML;
              var certNoNum = $(this).find(".CertNoData")[0].innerHTML;
              // var employeeIDNum = 100;
              // var certNoNum = 200;
              var markup = "<li>Employee ID = "+employeeIDNum+", CertNo = "+certNoNum+"</li>";
              $("#mismatchList").append(markup);
              numMismatch += 1;
            }
        });
        $("#mismatchCount")[0].innerHTML = numMismatch;
    }


    function sortTable(f,n){
        var rows = $('#xrefTable tbody tr').get();

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
            $('#xrefTable').children('tbody').append(row);
        });
    }

    var f_name = 1;
    var f_hour = 1;

    $(".nameSort").on("click",function() {
        var numCol = $(this).closest("tr").children().index($(this).closest("th"));
        f_name *= -1;
        sortTable(f_name,numCol);
    });

    $(".numSort").on("click",function() {
        var numCol = $(this).closest("tr").children().index($(this).closest("th"));
        f_hour *= -1;
        sortTable(f_hour,numCol);
    });

});
