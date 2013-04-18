

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
             $facebookEnable=isset($curlResponse['facebookConnected'])?$curlResponse['facebookConnected']:"N";
             $twitterEnable=isset($curlResponse['twitterConnected'])?$curlResponse['twitterConnected']:"N";
             $googleEnable=isset($curlResponse['googleConnected'])?$curlResponse['googleConnected']:"N";
             $linkedinEnable=isset($curlResponse['linkedinConnected'])?$curlResponse['linkedinConnected']:"N";
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
    <div style="float:left;width:100%">
        <img src="<?php echo plugins_url('images/personaLogo.png', dirname(dirname(__FILE__))); ?>" alt="" style="float:left;margin-right: 10px;"/>
        <span style="font:normal 24px Arial;color:#333333;float:left;margin-top: 5px;">Persona Configuration</span>
    </div>
    <div style="float: left;width: 100%;font: normal 13px Arial;color: #333333;margin-top: 20px;">
        <span>Persona from BetaOUT provides social login, single sign on , social apps for your multiple Wordpress sites.</span><br/>
        <span>This plugin will allow users to register with social networks like Twitter, Facebook and comment, share, rate easily.</span>
    </div>
    <div style="float: left;width: 100%;">
        <div style="background-color: #A9D5ED;font: normal 13px Arial;color: #333333;padding: 5px;margin: 30px 0 5px 30px;float: left">
            <span>BetaOUT API Key for (<a href="<?php echo get_bloginfo('url'); ?>" style="font-style: italic;color: #797979;text-decoration: none"><?php echo get_bloginfo('url'); ?></a>)</span>
        </div>
    </div>


    <?php
    if ($showError) {
        ?>
        <div style="width: 100%;float: left">
            <div style="min-height:25px;background-color:#f2dede;margin-left:30px;padding-top:5px;text-align: center;width:315px;-moz-border-radius:10px;border-radius:10px;" id="errorDiv">
                <img id="errorDivImage" style="float:right;margin-right:3px;margin-top: -1px;cursor:pointer;" src="<?php echo plugins_url('images/closeIcon.png', dirname(dirname(__FILE__))); ?>"/>
                <span style="font-family: Arial;color:#b94a48;"><?php echo $curlResponse['error']; ?></span>
            </div>
        </div>

        <?php
    }
    ?>


    <div style="float: left;width: 100%">
        <form action="" method="post" id="betaoutApiForm" style="width: 370px;margin-left: 30px">
            <fieldset>
                <div class="control-group">
                    <span>Site API Key</span>
                    <input type="text" value="<?php if ($showError) echo get_option('_PERSONA_API_KEY_TEMP', false); else echo get_option('_PERSONA_API_KEY', false); ?>" id="betaoutApiKey" name="betaoutApiKey" class="inputText"/>
                </div>
                <div  class="control-group">
                    <span>Site API Secret</span>
                    <input type="text" value="<?php if ($showError) echo get_option('_PERSONA_API_SECRET_TEMP', false); else echo get_option('_PERSONA_API_SECRET', false); ?>" id="betaoutApiSecret" name="betaoutApiSecret" class="inputText"/>
                </div>
                <div class="control-group">
                    <input type="hidden" name="wordpressBoPluginUrl" id="wordpressBoPluginUrl" value="<?php echo plugins_url(); ?>/socialcloud"/>
                    <input type="hidden" name="wordpressVersion" id="wordpressVersion" value="<?php echo get_bloginfo('version'); ?>"/>
                    <input type="hidden" name="betaoutSubmit" value="submit"/>
                    <div style="float: right">
                        <a href="http://access.betaout.com/persona/sign-up" target="_blank">Get Your Free Key</a>
                        <button type="button" onclick="javascript:submit();" class="buttonStyle">Save Key</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div style="float: left;width: 100%;font: normal 13px Arial;color: #333333;margin: 20px;">
        <img src="<?php echo plugins_url('images/bulb.png', dirname(dirname(__FILE__))); ?>" alt="" style="float: left;margin-right: 10px"/>
        <div style="float: left">
            <span>To be able to use this plugin you first of all need to create a free account at <a href="http://access.betaout.com/persona/sign-up">http://access.betaout.com/persona/sign-up</a>  and setup a Site.</span>
            <br/><span>After having created your account and adding your Site, get your Site API key and Secret.</span>
        </div>

    </div>
</div>
