<?php

/**
 * Copyright 2011 Instapress, Inc.
 * @author Jitendra Singh Bhadouria
 *
 */
require_once 'IPPHPSDKBase.php';

class IPPHPSDK extends IPPHPSDKBase {

    public function __construct($apiKey, $apiSecret, $publicationUrl) {
        parent::__construct($apiKey, $apiSecret, $publicationUrl);
    }

}

?>
