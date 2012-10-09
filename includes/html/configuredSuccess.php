
<div style="padding: 20px;width:750px;color:#333">
    <span>
        <img src="<?php echo plugins_url('images/cloudSmall.png', dirname(dirname(__FILE__))); ?>" alt="" style="vertical-align: middle;margin-right: 20px;"/>
        <span style="font:normal 24px Arial">BetaOUT Configuration</span>
    </span>
    <div style="font: normal 13px Arial">
        <span>BetaOUT provides content and social infrastructure to your content network running on Wordpress.</span><br/>
        <span>It seamlessly support multiple sites running on wordpress platform.</span>
    </div>
    <div style="font:normal 24px Arial;margin-top:40px; padding-bottom:20px; border-bottom:1px solid #ccc;">BetaOUT Plugin successfully installed</div>
    <div style=" margin:15px 0;font: normal 13px Arial">
        <span>Editors can login at <a href="http://<?php echo $clientAccountName; ?>.newsroom.to/" target="_blank" class="anchorTxt">Newsroom</a> </span><br/><br/>
        <span>Contributors and Other Staff can login at <a href="http://<?php echo $clientAccountName; ?>.workbench.to/" target="_blank"  class="anchorTxt"> Workbench</a> </span><br/><br/>
        <span>User Profiles and User Dashboard can be found at <a href="http://<?php echo $clientAccountName; ?>.persona.to/" target="_blank" class="anchorTxt">Persona</a> </span><br/><br/>
        <span>Betaout Admin Console can be found at <a href="http://access.betaout.com/" target="_blank" class="anchorTxt"> Access.Betaout.com</a> </span><br/><br/>
    </div>
    <a href="http://<?php echo str_replace('&action=changekey', '', $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']); ?>&action=changekey" style="color:#0000FF; text-decoration:none;float:right">Change Site API key</a>
    <!--<div class="borderBtm"></div>-->
</div>
