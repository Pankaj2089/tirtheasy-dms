@php
$siteUrl = env('APP_URL');
@endphp
@extends('layout.default')
@section('content')
  <div class="wrapper login" style="background: url('{{$siteUrl}}public/assets/img/login-img.jpg') no-repeat;background-size: contain;">
    <div class="row align-items-center">
        <div class="col-md-5 bg-white ms-auto">
            <div class="login-form text-center">
                <div class="form">
                    <img src="{{$siteUrl}}public/assets/img/logo.svg" class="login-logo" alt="">
                    <h3>Welcome Back</h3>
                    <p class="sub-hd">Let’s Login to grab amazing deal</p>

                    <form id="adminLoginForm">
                        <div class="text-start">
                            <label for="code" class="form-label">Property Code</label>
                            <input type="text" class="form-control" id="property_code" name="property_code" placeholder="Enter property code">
                        </div>
                        <div class="text-start">
                            <label for="login_id" class="form-label">Login ID</label>
                            <input type="text" class="form-control" id="login_id" name="login_id" placeholder="Enter login id">
                        </div>

                         <div class="text-start position-relative">
                            <label for="Password" class="form-label">Password</label>
                            <img src="{{$siteUrl}}public/assets/img/eye.svg" class="show-ps"  id="password-eye" alt="">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                        </div>
                       
                        <div class="text-start">
                             <button type="button" class="me-auto theme-btn"  id="loginSubmit">Login</button>
                        </div>

                        <!-- <p class="sub-lg mb-0">Don’t have an account <a id="loginSubmit" style="cursor:pointer">Signup</a></p>
                        <hr style="display: none;"> -->
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>
  <script type="text/javascript">
    let adminLoginURL = "{{url('vendor-login')}}"; 
    let dashboardURL = "{{url('/calendar')}}";
    $(document).ready(function(){
      $("#property_code", "#login_id, #password").on('keyup', function (e) {
        if (e.keyCode == 13) {
          $('#loginSubmit').trigger('click');
        }
      });
      $('#loginSubmit').click(function(e){
        var flag = 0;
        if($.trim($("#property_code").val()) == ''){
          flag = 1;
          showMessage('Please enter your property code.');
          return false;
        }else if($.trim($("#login_id").val()) == ''){
          flag = 1;
          showMessage('Please enter your login ID.');
          return false;
        }else if($.trim($("#password").val()) == ''){
          flag = 1;
          showMessage('Please enter your Password.');
          return false;
        }
        if(flag == 0){
          $('#loginSubmit').html('Processing...');
          $.ajax({
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: adminLoginURL,
            data: $('#adminLoginForm').serialize(),
            beforeSend:function(){$('#loginSubmit').removeClass('login-btn').addClass('login-btn-processing').val('Processing...'); },
            success: function(msg){
              var obj = JSON.parse(msg);
              $('#loginSubmit').html('Log In');
              if(obj['heading'] == "Success"){
                window.location.assign(dashboardURL);
              }else{
                showMessage(obj['msg']);
                return false;
              }
            },error: function(ts) {
              showMessage('Some thing want to wrong, please try after sometime.');
              return false;
            }
          });
        }
      });
       $('#password-eye').on('click', function () {
        let input = $('#password');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            $(this).attr('src', '{{$siteUrl}}public/assets/img/eye.svg'); // optional
        } else {
            input.attr('type', 'password');
            $(this).attr('src', '{{$siteUrl}}public/assets/img/eye.svg'); // optional
        }
    });

    });
    function showMessage(msg){
      swal("Error!", msg, "error");
    }
    </script>
@endsection