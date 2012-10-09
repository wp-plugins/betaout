<link rel='stylesheet' href="<?php echo plugins_url('cc_html/style.css', dirname(__FILE__)); ?>" type='text/css' media='all' />

<h2><i class="icon-userRole-large"></i> User Roles</h2>
<div class="betaout">
	<div class="userRoles">
		<?php 
			$roleId = isset( $_GET[ 'role' ] ) ? $_GET[ 'role' ] : 0;
			try{
				$roles = ccPages::getRoles();
				$clientBOSiteLink = ccPages::$boSiteLink;
			}catch ( Exception $ex ){
				echo $ex->getMessage();
				exit;
			}
			$totalRoles = count( $roles );
			if( $totalRoles == 0 ){
		?>
				<div>No record found !</div>
		<?php 		
			}else{
				for( $i=0; $i < $totalRoles; $i++ ){
		?>
					<h3><a class="singleUserRole" href="<?php echo $clientBOSiteLink . $roles[ $i ][ 'link' ]?>"><?php echo $roles[ $i ][ 'name' ]?></a></h3>
		<?php
				}
			}
		?>
	</div>
</div><!-- /.betaout -->