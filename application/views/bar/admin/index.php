<!doctype html>
<html>
<head>
	<title>业务逻辑</title>
	<script type="text/javascript" src="<?php echo STYLE_PATH; ?>/js/jquery-3.3.1.js"></script>

</head>
  <body>
  	<input type="text" name="id">
  	<input type="text" name="phone">
    <div class="log">search</div>
    <div class="show"></div>
    <div class="show2"></div>
    

    <script>
    	var cur_user_id = 0;
    	$(".log").on('click',function(){
    		var id = $("input[name=id]").val();
    		var phone = $("input[name=phone]").val();
    		$.ajax({
				type: "POST",
				url: "<?=ADMIN_PATH?>/user",
				data: {
					user_id:id, 
					phone:phone
				},
				dataType: "json",
				success: function(data){
					console.log(data);
				    $(".show").html(data.message);
				    $(".show2").html(data.data);
				    cur_user_id = data.data.uid;
				}
         	});
    	});
    </script>
  </body>
</html>