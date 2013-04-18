

<?php
$showError = false;
if (isset($_POST['betaoutSubmit'])) {
    try {
        $betaoutApiKey = isset($_POST['betaoutApiKey']) ? trim($_POST['betaoutApiKey']) : '';
        $betaoutApiSecret = isset($_POST['betaoutApiSecret']) ? trim($_POST['betaoutApiSecret']) : '';
        $wordpressVersion = isset($_POST['wordpressVersion']) ? trim($_POST['wordpressVersion']) : '';
        $wordpressBoPluginUrl = isset($_POST['wordpressBoPluginUrl']) ? trim($_POST['wordpressBoPluginUrl']) : '';

        $curlResponse = SocialAxis_UserDataManagement::validateWordpressSite($betaoutApiKey, $betaoutApiSecret, $wordpressVersion, $wordpressBoPluginUrl);
        if (isset($curlResponse['responseCode']) && $curlResponse['responseCode'] == 200) {
            $clientAccountName = $curlResponse['clientAccountName'];
            require_once('configuredSuccess.php');
            return;
        }
        else
            $showError = true;
    } catch (Exception $ex) {

    }
}
?>

<div style="padding: 20px;">
    <span>
        <img src="<?php echo plugins_url('images/cloudSmall.png', dirname(dirname(__FILE__))); ?>" alt="" style="vertical-align: middle;margin-right: 20px;"/>
        <span style="font:normal 24px Arial;color:#333333;">BetaOUT Configuration</span>
    </span>
    <div style="font: normal 13px Arial;color: #333333;">
        <span>BetaOUT provides content and social infrastructure to your content network running on Wordpress.</span><br/>
        <span>It seamlessly support multiple sites running on wordpress platform.</span>
    </div>
    <div style="background-color: #A9D5ED;display: inline-block;font: normal 13px Arial;color: #333333;padding: 5px;margin: 30px;">
        <span>BetaOUT API Key for (<a href="<?php echo get_bloginfo('url'); ?>" style="font-style: italic;color: #797979;text-decoration: none"><?php echo get_bloginfo('url'); ?></a>)</span>
    </div>

    <?php
    if ($showError) {
        ?>
        <div style="min-height:25px;background-color:#f2dede;margin-left:30px;padding-top:5px;text-align: center;width:315px;-moz-border-radius:10px;border-radius:10px;" id="errorDiv">
            <img id="errorDivImage" style="float:right;margin-right:3px;margin-top: -1px;cursor:pointer;" src="<?php echo plugins_url('images/closeIcon.png', dirname(dirname(__FILE__))); ?>"/>
            <span style="font-family: Arial;color:#b94a48;"><?php echo $curlResponse['error']; ?></span>
        </div>
        <?php
    }
    ?>


    <div style="margin: 0px 30px;">
        <form action="" method="post" id="betaoutApiForm" style="width: 350px">
            <fieldset>
                <div class="control-group">
                    <span>Key</span>
                    <input type="text" value="<?php if($showError) echo get_option('_BETAOUT_API_KEY_TEMP', false); else echo get_option('_BETAOUT_API_KEY', false); ?>" id="betaoutApiKey" name="betaoutApiKey" class="inputText"/>
                </div>
                <div  class="control-group">
                    <span>Secret</span>
                    <input type="text" value="<?php if($showError) echo get_option('_BETAOUT_API_SECRET_TEMP', false); else echo get_option('_BETAOUT_API_SECRET', false); ?>" id="betaoutApiSecret" name="betaoutApiSecret" class="inputText"/>
                </div>
                <div class="control-group">
                    <input type="hidden" name="wordpressBoPluginUrl" id="wordpressBoPluginUrl" value="<?php echo plugins_url(); ?>/betaout"/>
                    <input type="hidden" name="wordpressVersion" id="wordpressVersion" value="<?php echo get_bloginfo('version'); ?>"/>
                    <input type="hidden" name="betaoutSubmit" value="submit"/>
                    <div style="float: right">
                        <a href="http://access.betaout.com/admin-signup/nv/packageId/1" target="_blank">Get Your Free Key</a>
                        <button type="button" onclick="javascript:submit();" class="buttonStyle">Save Key</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
