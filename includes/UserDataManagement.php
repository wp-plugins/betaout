<?php

/**
 * @author Jitendra Singh Bhadouria
 */
class SocialAxis_UserDataManagement {

    /**
     * @author Jitendra Singh Bhadouria
     * @desc Retreive all wordpress users 
     * @return array of wordpress users
     */
    public static function getWordpressUsers() {
        global $wpdb;
        try {
            $users = get_users();
            $i = 0;
            $userDataArray = array();
            foreach ($users as $val) {
                 $wp_user = new WP_User($val->ID);
                 if (in_array( 'subscriber', $wp_user->roles ) )
                         continue;
                $userDataArray[$i]['wordpressUserId'] = $val->ID;
                $userDataArray[$i]['login'] = $val->user_login;
                $userDataArray[$i]['email'] = $val->user_email;
                $metaData = get_metadata(user, $val->ID);
                $userDataArray[$i]['firstName'] = !empty($metaData['first_name'][0]) ? $metaData['first_name'][0] : $val->display_name;
                $userDataArray[$i]['lastName'] = !empty($metaData['last_name'][0]) ? $metaData['last_name'][0] : $val->display_name;
                $userDataArray[$i]['status'] = 'active';
                $userRoleArray = unserialize($metaData[$wpdb->prefix . 'capabilities'][0]);
                $userRoleArray = array_keys($userRoleArray);
                $userDataArray[$i++]['role'] = $userRoleArray[0];
            }
            return $userDataArray;
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc update personaUserId as meta data of user
     * @return nothing
     */
    public static function updatePersonaUserId($userArray) {
        try {
            $userArray = json_decode($userArray, true);
            if (is_array($userArray) && count($userArray) > 0) {
                foreach ($userArray as $value) {
                    if ($value['personaUserId'] && isset($value['wordpressUserId'])) {
                        update_metadata('user', $value['wordpressUserId'], 'personaUserId', $value['personaUserId']);
                    }
                }
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc It will print the error message in appropriate format(i.e., usually in json) and terminate the program
     * @return nothing
     */
    public static function errorFormatingResponse($errorMessage, $responseCode, $format = 'json') {
        try {
            switch ($format) {
                case 'jsonp':
                    echo $_GET['callback'] . '({"error":"' . $errorMessage . '", "responseCode":' . $responseCode . '});';
                    die;
                default:
                    echo '{"error":"' . $errorMessage . '","responseCode":' . $responseCode . '}';
                    die;
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc match the requested hash with calculated hash, terminate the program if hash mismatched otherwise proceed
     * @return nothing
     */
    public static function checkHash($url, $hash) {
        try {
            $url = explode("?", $url);
            $encryptionKey = self::encode_rfc3986(get_option("_BETAOUT_API_SECRET"));
            $ourHash = base64_encode(hash_hmac('sha1', $url[1], $encryptionKey, true));
            if ($ourHash != $hash)
                self::errorFormatingResponse('Request can not be fulfilled due to hash mismatched!', 501);
        } catch (Exception $ex) {
            
        }
    }

    public static function encode_rfc3986($string) {
        return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode(($string))));
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc It will add custom roles to wordpress according to betaout and add capabilites to each role like 'contributor' capabilities
     * @return nothing
     */
    public static function addCustomUserRoles() {
        try {
            $roleArray = array('copy editor' => 'Copy Editor', 'proof reader' => 'Proof Reader', 'multimedia' => 'Multimedia');
            $contributorCapabilities = get_role('contributor');
            foreach ($roleArray as $role => $display_name) {
                add_role($role, $display_name, $contributorCapabilities->capabilities);
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc It will generate unique login name
     * @return nothing
     */
    public static function createUniqueLoginName($userFirstName, $userLastName, $userLogin) {
        try {
            $isUserExists = get_user_by('login', $userLogin);
            if (!$isUserExists)
                return $userLogin;
            $userLogin = $userFirstName . "-" . $userLastName;
            $userLogin = $tempUserLogin = str_replace(" ", "-", $userLogin);
            $i = 0;
            do {
                $userLogin = ($i == 0) ? $userLogin : $tempUserLogin . $i;
                $isUserExists = get_userdatabylogin($userLogin);
                $i++;
            } while ($isUserExists);
            return $userLogin;
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc it is an function bind with the user_register hook and send the user data to persona as soon as it successfully register on wordpress
     * @return nothing
     */
    public static function sendNewUserToPersona($user_id) {
        try {
            $user = new WP_User($user_id);
            $userDataArray[0]['wordpressUserId'] = $user_id;
            $userDataArray[0]['login'] = $user->data->user_login;
            $userDataArray[0]['email'] = $user->data->user_email;
            $userDataArray[0]['firstName'] = get_user_meta($user_id, 'first_name', true);
            $userDataArray[0]['lastName'] = get_user_meta($user_id, 'last_name', true);
            $userDataArray[0]['role'] = $user->roles[0];
            $socialAxisKey = get_option("_BETAOUT_API_KEY");
            $socialAxisSecret = get_option("_BETAOUT_API_SECRET");
            $parameters = array('userDataArray' => $userDataArray);
            $IPPHPSDKObj = new IPPHPSDK($socialAxisKey, $socialAxisSecret, "http://persona.to/clientapi/");
            $returArray = $IPPHPSDKObj->registerWordpressUser($parameters);
            self::updatePersonaUserId($returArray);
        } catch (Exception $ex) {
            
        }
    }

    public static function editUser($user_id) {
        try {
            $user = new WP_User($user_id);
            $userDataArray['email'] = $user->data->user_email;
            $userDataArray['firstName'] = get_user_meta($user_id, 'first_name', true);
            $userDataArray['lastName'] = get_user_meta($user_id, 'last_name', true);
            $userDataArray['personaUserId'] = get_user_meta($user_id, 'personaUserId', true);
            $userDataArray['role'] = $user->roles[0];
            $socialAxisKey = get_option("_BETAOUT_API_KEY");
            $socialAxisSecret = get_option("_BETAOUT_API_SECRET");
            $parameters = array('userDataArray' => $userDataArray);
            $IPPHPSDKObj = new IPPHPSDK($socialAxisKey, $socialAxisSecret, "http://persona.to/clientapi/");
            $IPPHPSDKObj->editWordpressUser($parameters);
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc accept userRole from wordpress and return appropriate userRole for persona
     * @param string $userRole required
     */
    public static function getUserRoleForWordpress($userRole) {
        try {
            $userRoleArray = array('editor', 'copy editor', 'multimedia', 'proof reader', 'administrator', 'subscriber', 'author');
            return in_array($userRole, $userRoleArray) ? $userRole : (($userRole == 'writer' ) ? 'author' : 'contributor');
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc call exactly when the plugin will be activated and take appropriate action
     */
    public static function myplugin_activate() {
        try {
            $betaoutApiKey = get_option("_BETAOUT_API_KEY");
            $betaoutApiSecret = get_option("_BETAOUT_API_SECRET");
            if (!empty($betaoutApiKey) && !empty($betaoutApiSecret))
                self::validateWordpressSite($betaoutApiKey, $betaoutApiSecret);
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc check if wordpress plugin installed, if not again validate website and import and export users,etc
     */
    public static function checkIfWpSync() {
        try {
            $betaoutApiKey = get_option("_BETAOUT_API_KEY");
            $betaoutApiSecret = get_option("_BETAOUT_API_SECRET");
            if (!empty($betaoutApiKey) && !empty($betaoutApiSecret)) {
                $IPPHPSDKObj = new IPPHPSDK($betaoutApiKey, $betaoutApiSecret, ACCESS_API_URL);
                $response = $IPPHPSDKObj->isWpSync();
                $curlResponse = json_decode($response, true);
                if (isset($curlResponse['isWpSync']) && $curlResponse['isWpSync'] == 'N') {
                    self::myplugin_activate();
                }
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc call exactly when the plugin will be deactivated and take appropriate action
     */
    public static function myplugin_deactivate() {
        try {

            //added to deactivate contentcloud
            contentcloud::cc_plugin_deactivated();

            $betaoutApiKey = get_option("_BETAOUT_API_KEY");
            $betaoutApiSecret = get_option("_BETAOUT_API_SECRET");
            $IPPHPSDKObj = new IPPHPSDK($betaoutApiKey, $betaoutApiSecret, ACCESS_API_URL);
            $IPPHPSDKObj->deactivateBOPlugin();
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc call exactly when the plugin will be deactivated and take appropriate action
     */
    public static function myplugin_uninstall() {
        try {
            $betaoutApiKey = get_option("_BETAOUT_API_KEY");
            $betaoutApiSecret = get_option("_BETAOUT_API_SECRET");
            $IPPHPSDKObj = new IPPHPSDK($betaoutApiKey, $betaoutApiSecret, ACCESS_API_URL);
            $response = $IPPHPSDKObj->uninstallBOPlugin();
            $curlResponse = json_decode($response, true);
            if (isset($curlResponse['responseCode']) && $curlResponse['responseCode'] == 200) {
                delete_option("_BETAOUT_API_KEY");
                delete_option("_BETAOUT_API_SECRET");
                delete_option("_BETAOUT_API_KEY_TEMP");
                delete_option("_BETAOUT_API_SECRET_TEMP");
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @author Jitendra Singh Bhadouria
     * @desc call exactly when the plugin will be deactivated and take appropriate action
     */
    public static function getProfileFromPersonaUserId($personaUserId) {
        try {
            $socialAxisKey = get_option("_BETAOUT_API_KEY");
            $socialAxisSecret = get_option("_BETAOUT_API_SECRET");
            $parameters = array('userId' => $personaUserId);
            $IPPHPSDKObj = new IPPHPSDK($socialAxisKey, $socialAxisSecret, PERSONA_API_URL);
            $userProfile = $IPPHPSDKObj->getUserProfile($parameters);
            $userProfile = json_decode($userProfile, true);
            if (isset($userProfile['responseCode']) && $userProfile['responseCode'] == 200) {
                $userProfile['personaUserId'] = $personaUserId;
                $userDataArray = array(0 => $userProfile);
                self::insertOrUpdateUser($userDataArray);
            }
        } catch (Exception $ex) {
            
        }
    }

    public static function insertOrUpdateUser($userDataArray) {
        try {
            if (!is_array($userDataArray))
                self::errorFormatingResponse('userDataArray should be an array of user', 501);
            $error = array();
            foreach ($userDataArray as $userData) {
                if (isset($userData['userEmail'])) {
                    $userData['userRole'] = self::getUserRoleForWordpress($userData['userRole']);
                    if (email_exists($userData['userEmail'])) {
                        $user = get_user_by('email', $userData['userEmail']);
                        $user = new WP_User($user->ID);
                        if ($user->roles[0] != "administrator" && $user->roles[0] != "admin") {
                            wp_update_user(array('ID' => $user->ID, 'role' => $userData['userRole']));
                        } else {
                            
                        }
                    } else {
                        if (isset($userData['userEmail']) && isset($userData['personaUserId']) && isset($userData['userLogin']) && isset($userData['userRole'])) {
                            $userData['userLogin'] = self::createUniqueLoginName($userData['userFirstName'], $userData['userLastName'], $userData['userLogin']);
                            $isUserRegistered = wp_insert_user(array('first_name' => $userData['userFirstName'], 'last_name' => $userData['userLastName'], 'user_email' => $userData['userEmail'], 'user_pass' => $userData['userPassword'], 'user_login' => $userData['userLogin'], 'role' => $userData['userRole']));
                            if (is_numeric($isUserRegistered)) {
                                update_metadata('user', $isUserRegistered, 'personauserId', $userData['personaUserId']);
                            }
                            else
                                $error[] = $isUserRegistered;
                        }
                        else
                            self::errorFormatingResponse("either of these fields(userFirstName, userLastName, userEmail, userPassword, personaUserId, userLogin, userRole) are empty", 403);
                    }
                }
                else
                    self::errorFormatingResponse("either of these fields(userFirstName, userLastName, userEmail, userPassword, personaUserId, userLogin, userRole) are empty", 403);
            }
            if (is_array($error) && count($error) > 0)
                self::errorFormatingResponse(json_encode($error), 403);
        } catch (Exception $ex) {
            
        }
    }

    public static function validateWordpressSite($betaoutApiKey = '', $betaoutApiSecret = '', $wordpressVersion = '', $wordpressBoPluginUrl = '') {
        try {
            $betaoutApiKey = empty($betaoutApiKey) ? get_option("_BETAOUT_API_KEY") : $betaoutApiKey;
            $betaoutApiSecret = empty($betaoutApiSecret) ? get_option("_BETAOUT_API_SECRET") : $betaoutApiSecret;
            $wordpressVersion = empty($wordpressVersion) ? get_bloginfo('version') : $wordpressVersion;
            $wordpressBoPluginUrl = empty($wordpressBoPluginUrl) ? plugins_url() . "/betaout" : $wordpressBoPluginUrl;
            $parameters = array('wordpressVersion' => $wordpressVersion, 'wordpressBoPluginUrl' => $wordpressBoPluginUrl);
            update_option("_BETAOUT_API_KEY_TEMP", $betaoutApiKey);
            update_option("_BETAOUT_API_SECRET_TEMP", $betaoutApiSecret);
            self::addCustomUserRoles();
            try {
                $IPPHPSDKObj = new IPPHPSDK($betaoutApiKey, $betaoutApiSecret, ACCESS_API_URL);
                $curlResponse = $IPPHPSDKObj->validatePublication($parameters);
            } catch (Exception $ex) {
                $curlResponse = '{ "error": "' . $ex->getMessage() . '", "responseCode": 500 }';
            }
            $curlResponse = json_decode($curlResponse, true);

            if (isset($curlResponse['responseCode']) && $curlResponse['responseCode'] == 200) {
                update_option("_BETAOUT_API_KEY", $betaoutApiKey);
                update_option("_BETAOUT_API_SECRET", $betaoutApiSecret);
                update_option("_BETAOUT_API_KEY_TEMP", '');
                update_option("_BETAOUT_API_SECRET_TEMP", '');
//                self::addCustomUserRoles();
                $userDataArray = self::getWordpressUsers();
                $parameters = array('userDataArray' => $userDataArray);
                $IPPHPSDKObj = new IPPHPSDK($betaoutApiKey, $betaoutApiSecret, PERSONA_API_URL);
                $IPPHPSDKObj->sendPreviousUser();
                $returArray = $IPPHPSDKObj->registerWordpressUser($parameters);
                self::updatePersonaUserId($returArray);
            }
        } catch (Exception $ex) {
            
        }

        return $curlResponse;
    }

}

?>
