layui.use(['form','layer','jquery'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer
        $ = layui.jquery;
     $(document).keyup(function(){
        if(event.keyCode==13){
          login();
        }
    });
     // 点击登录
    $("#login").click(function(){
       login();
    })
     function login(){
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;
        $.ajax({
            type: "POST",
            dataType: "json",  
            url: "/tool/login",          
            data: {
                username: username,
                password: password
            }, 
            success: function (result) {
                console.log(result); 
                console.log($('#form').serialize());
                if (result.status == "fail") {
                    layer.alert(result.detail);
                }else{
                     window.location.href = "/SignUpCMS/index.html";
                };
            },
            error : function(result) {
                alert("error异常！");
            }
        });
     }
    //表单输入效果
    $(".loginBody .input-item").click(function(e){
        e.stopPropagation();
        $(this).addClass("layui-input-focus").find(".layui-input").focus();
    })
    $(".loginBody .layui-form-item .layui-input").focus(function(){
        $(this).parent().addClass("layui-input-focus");
    })
    $(".loginBody .layui-form-item .layui-input").blur(function(){
        $(this).parent().removeClass("layui-input-focus");
        if($(this).val() != ''){
            $(this).parent().addClass("layui-input-active");
        }else{
            $(this).parent().removeClass("layui-input-active");
        }
    })
})
