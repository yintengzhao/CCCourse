$(function(){


  $("#submit").click(function(){

    var _account = $('#account').val();
    var _password = $('#password').val();

    var data = $(this).serialize();
    $.post("/index.php/Admin/Admin/login", 
      {account: _account,password: _password},
      function(data, textStatus){
        if(data.status){
          window.location.href = '/admin/';
        } else {
          alert(data.info);
        }
      });
  });

})