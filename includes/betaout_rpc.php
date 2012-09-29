<?php

/**
 * @author JItendra Singh Bhadouria
 */
class betaout_rpc {

    /**
     * @author Jitendra Singh Bhadouria
     * @desc insert the persona user to wordpress
     * @return nothing
     */
    public static function insertwordpressuser() {
        try {
            $userDataArray = isset($_REQUEST['userDataArray']) ? $_REQUEST['userDataArray'] : '';
            SocialAxis_UserDataManagement::insertOrUpdateUser($userDataArray);
        } catch (Exception $ex) {
            
        }
    }

}

?>
