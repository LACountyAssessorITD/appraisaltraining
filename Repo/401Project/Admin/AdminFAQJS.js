$(document).ready(function(){

  $("#faqTab").on("click",function(e) {
      e.preventDefault();
  });


  $(".questionButton").on("click",function() {
      if($(this).data("clicked")) {
        $(this).closest(".faqSetDiv").find(".answerDiv").hide();
        $(this).data("clicked",false);
        $(this).find("i").remove();
        $(this).find("span").append("<i class='fa fa-angle-down' aria-hidden='true'></i>")
      }
      else {
        $(this).closest(".faqSetDiv").find(".answerDiv").show();
        $(this).data("clicked",true);
        $(this).find("i").remove();
        $(this).find("span").append("<i class='fa fa-angle-up' aria-hidden='true'></i>")
      }
  });

});
