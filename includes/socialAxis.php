<?php

/**
 * Add the Css and Js files to the <head> of WordPress pages
 *
 * @return
  none
  adds HTML to the document <head>
 */
class socialAxis_plugin {

    public static function socialAxis_addFiles() {
        $src = plugins_url('css/common.css', dirname(__FILE__));
        wp_register_style('commonCss', $src);
        wp_enqueue_style('commonCss');
//        if (is_admin()) {

         wp_enqueue_script('json_library', plugins_url('js/jsonp_library.js'), array('jquery'), '', false);
//        }
       
    }

    public static function betaout() {
        try {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'changekey') {
                require_once('html/configuration.php');
            } else {
                $betaoutApiKey = get_option("_BETAOUT_API_KEY");
                $betaoutApiSecret = get_option("_BETAOUT_API_SECRET");
                $wordpressVersion = get_bloginfo('version');
                $wordpressBoPluginUrl = plugins_url() . "/betaout";

                if (!empty($betaoutApiKey) && !empty($betaoutApiSecret)) {
                    $parameters = array('wordpressVersion' => $wordpressVersion, 'wordpressBoPluginUrl' => $wordpressBoPluginUrl);
                    try {
                        $IPPHPSDKObj = new IPPHPSDK($betaoutApiKey, $betaoutApiSecret, ACCESS_API_URL);
                        $curlResponse = $IPPHPSDKObj->validatePublication($parameters);
                    } catch (Exception $ex) {
                        $curlResponse = '{ "error": "' . $ex->getMessage() . '", "responseCode": 500 }';
                    }
                    $curlResponse = json_decode($curlResponse, true);
                }
                if (isset($curlResponse['responseCode']) && $curlResponse['responseCode'] == 200) {
                    $clientAccountName = $curlResponse['clientAccountName'];
                    require_once('html/configuredSuccess.php');
                }
                else
                    require_once('html/configuration.php');
            }
        } catch (Exception $ex) {
            
        }
    }

    public static function post_myfunction($postId='') {
        try {
            global $post;
            $authorEmail = get_the_author_meta('user_email', $post->post_author);
            $objBase = new IPPHPSDK(get_option("_SOCIAL_AXIS_KEY", false), get_option("_SOCIAL_AXIS_SECRET", false));
            $parameters = array('activityName' => 'MINE_STORY_CREATE', 'contentId' => $post->ID, 'userEmail' => $authorEmail);
            $result = $objBase->requestGamification($parameters);
        } catch (Exception $ex) {
            
        }
    }

    public static function socialAxisPluginMenu() {
        add_menu_page('BetaOut', 'BetaOut', 'manage_options', 'betaout', 'socialAxis_plugin::betaout', plugins_url('images/icon.png', dirname(__FILE__)));
        ccPages::addMenu('betaout');
       // add_submenu_page('betaout', 'Persona', 'Persona', 'manage_options', 'persona', 'socialAxis_plugin::persona');
    }

//    public static function socialAxisAddShareButtons($content='') {
//        global $post;
//        return $content . '<div class="engage_socialShareBar">
//            
//            <script type="text/javascript">
//                 engage.socialShareBarData={
//                    engage_socialShareBarType:"none",
//                    engage_socialShareBarApiKey:"n4jh5uc3ju83z052f85jgudj89bj5874ssjhf48jjo",
//                    engage_socialShareBarpinItImageUrl:"http://www.example.com/example.jpg",
//                    engage_socialShareBarshareUrl:"' . get_permalink($post->ID) . '",
//                    engage_socialShareBarTwitterhashtags:"",
//                    engage_socialShareBartwitterdatavia:"",
//                    engage_socialShareBarTextData:"",
//                    engage_socialShareBarDisplayView:"Horizontal",
//                    engage_socialShareBarButtons:"facebook,twitter,linked,google,Pinit,"
//                }
//               engage.socialShareBarModule.socialShareBarWidgetLoad()
//            </script>
//        </div> ';
//    }

//    public static function claimBadgePopUp() {
////        echo '<script type="text/javascript">engage.automaticPopupModule.automaticPopupLoad("n4jh5uc3ju83z052f85jgudj89bj5874ssjhf48jjo");</script>';
//    }

//    public static function bottomBar() {
//        echo '<div id="footerInfoContainer"></div>';
//        echo '<script type="text/javascript">engage.footerModule.footerWidgetLoad("n4jh5uc3ju83z052f85jgudj89bj5874ssjhf48jjo")</script>';
//    }

//    public static function socialAxisLogin_form() {
//        $html = '<script type="text/javascript" src="http://assets.betasa.info/js/commonForWordPressApi.js"></script>
//<div>
//<script type="text/javascript">
//engage.login={
//ButtonStyle:"engageLogos",
//callBackUrl:' . '"http://' . $_SERVER[SERVER_NAME] . '",
// apiKey:"'.get_option("_SOCIAL_AXIS_KEY",false).'",
// PluginPlacement:"onpage",
// buttonSize:"30",
// buttons:"facebook,twitter,linkdin,google,yahoo,windowLive "
//}
//engage.loginModule.loginWidgetLoad();
//</script>
//              <div id="engage_widgetLogin">
//<div id="engage_widgetResponceData"></div>
//</div>
//</div>';
//        echo $html;
//    }
}

/* EOF */