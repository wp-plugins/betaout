<div  style="background-color: #fff;padding: 20px;width: 800px;margin:auto ">
                <div class="row-fluid">
                    <img src="<?php echo plugins_url('/betaout/images/logo.png'); ?>" alt="AMPLIFY" title="AMPLIFY"/>
                </div>
                 <div class="row-fluid">
                    <img src="<?php echo plugins_url('/betaout/images/desktop.png'); ?>" alt="AMPLIFY" title="AMPLIFY" style="margin-left: 120px;"/>
                </div>
                <div id="amplify_api" class="row-fluid">
                    <div class="pull-left well-small" style="position:relative;background-color: #fefe4e;border:1px solid #fedb10;height: 114px;line-height: 25px">
                    <div class="span7" style="font-size: 22px;color: #666;font-weight: 800">
                        <p>To enable Content Cloud, you need to have an account at <a href="http://www.contentcloudhq.com"> contentcloudhq.com </a> and get your <strong>free</strong> API key and secret for this site</p>
                    </div>
                    <div class="pull-right">
                        <img src="<?php echo plugins_url('/betaout/images/u74_normal.png'); ?>" alt="" title=""/>
                    </div>
                    <div id="api" class="pull-right btn btn-large" style="position: absolute;right: 20px;bottom: 10px;background: #00aff8;color: #fff;text-shadow: none">
                        <?php $betaoutKey=get_option('_BETAOUT_API_KEY',false);
                        if(!empty($betaoutKey)){
                            echo '<span id="key">Change Api Key</span>';
                        }else{
                           echo ' <span id="key">I have my API key and Secret</span>';
                        }?>
                   </div>
                    </div>

                    <div  class="pull-left row-fluid" style="background-color: #fefebd;display: block;">
                        <div class="row-fluid control-group" style="color: #666;font-style: italic;">
                      <div>* Average time to signup, opening an account entering API key and secret takes about 27 seconds. :)</div>
                    </div>
                        <div class="key-box" style="display:none">
                            <form action="">
                                <div class="row-fluid control-group">
                                    <span class="span3">API Key</span>
                                    <input type="text" id="input_apikey" value="<?php echo get_option('_BETAOUT_API_KEY', false);?>"/>
                                    <span class="warning" id="fail" style="display:none"><i>* Please check your API key and Sercet</i></span>
                                    <span class="warning" id="success" style="display:none"><i>* key save successfully</i></span>
                                </div>
                                <div class="row-fluid control-group">
                                    <span class="span3">API Secret</span>
                                    <input type="text" id="input_apisecret" value="<?php echo get_option('_BETAOUT_API_SECRET', false);?>"/>
                                    <span class="warning">You can also mail us  at <a href="">support@betaout.com</a></span>
                                </div>
                     
                        <div class="row-fluid control-group">
                            <div class="span5" style="margin: 0 10px 0 5px">
                                <a href="" style="font-size: 13px;color: #666;margin-top:15px;float:right"><i>Cancel</i></a></div>
                            <input type="button" id="save_btn" name="submit" value="SAVE"/>
<!--                            <button id="save_btn" class="btn btn-success">SAVE</button>-->
                            <button id="wait_btn" class="btn" style="display: none;">WAIT..</button>
                        </div>
                            </form>
                        </div>
                </div>
                </div>
         
            </div>
    <script type="text/javascript" >
  jQuery("#key").click(function(){
  jQuery(".key-box").show();
});

jQuery(document).ready(function($) {
      $('#save_btn').click(function() {
        var apikey=$("#input_apikey").val();
        var seckey=$("#input_apisecret").val();
        
	var data = {
		action: 'verify_betaoutkey',
		betaoutApiKey:apikey,
                betaoutApiSecret:seckey,
               
	};

     $.post(ajaxurl, data, function(response) {
         if(response.status=="active"){
          $("#success").show();
           $("#fail").hide();
          }else{
          $("#fail").show();

          }
	},"json");
        });
});
</script>