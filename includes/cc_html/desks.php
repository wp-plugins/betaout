<link rel='stylesheet' href="<?php echo plugins_url('cc_html/style.css', dirname(__FILE__)); ?>" type='text/css' media='all' />

<h2><i class="icon-desk-large"></i> Desks</h2>
	<div class="betaout">
		<div class="row">
			<div class="column left-panel-column">
				<div class="left-panel">
					<!--<div class="left-panel-heading"></div>-->
					<div class="left-panel-nav">
						<?php
							$deskId = isset( $_GET[ 'desk' ] ) ? $_GET[ 'desk' ] : 0;
							$userRoleName = '';
							$deskEditLink = '';
							$deskName = '';
							$userRoleId = 0;
							try{
								$desks = ccPages::getDesks();
								$clientBOSiteLink = ccPages::$boSiteLink;
							}catch ( Exception $ex ){
								echo $ex->getMessage();
								exit;
							}
							$totalDesks = count( $desks );
							if( $totalDesks == 0 ){
						?>
								<div>No record found !</div>
						<?php
							}else{
								for( $i=0; $i < $totalDesks; $i++ ){
									if( $deskId == 0 ){
										$deskId = $desks[ 0 ][ 'id' ];
										$deskEditLink = $clientBOSiteLink . $desks[ 0 ][ 'link' ];
										$userRoleName = $desks[ 0 ][ 'deskRole' ];
										$userRoleId = $desks[ 0 ][ 'userRoleId' ];
										$deskName = $desks[ 0 ][ 'name' ];
									}elseif( $desks[ $i ][ 'id' ] == $deskId ){
										$deskEditLink = $clientBOSiteLink . $desks[ $i ][ 'link' ];
										$userRoleName = $desks[ $i ][ 'deskRole' ];
										$userRoleId = $desks[ $i ][ 'userRoleId' ];
										$deskName = $desks[ $i ][ 'name' ];
									}
						?>
									<a class="<?php echo $desks[ $i ][ 'id' ] == $deskId ? 'active' : '';?>" href="<?php echo "/wp-admin/admin.php?page=desks&desk=" . $desks[ $i ][ 'id' ];?>"><?php echo $desks[ $i ][ 'name' ];?></a>
						<?php
								}
							}
						?>
					</div>
				</div>
			</div>
			<div class="column right-panel-column">
				<div class="right-panel">
					<div class="tab-nav">
						<div class="tab-content">
							<div class="tab-content-header">
								<span><b><?php echo $deskName;?></b></span>
								<span class="grey alignright">User Role: <?php echo $userRoleName;?></span>
								<a class="note" href="<?php echo $deskEditLink;?>">To Add or Edit Workflow go to Newsroom</a>
								<div class="clear"></div>
							</div>
							<div class="tab-content-body desk-users">

								<?php
									try{
										$users = ccPages::getDeskUsers( $deskId, 1000 );
									}catch ( Exception $ex ){
										echo $ex->getMessage();
										exit;
									}
									$totalUsers = count( $users );
									if( $totalUsers == 0 ){
								?>
										<div>No user record found !</div>
								<?php
									}else{
										for( $i=0; $i < $totalUsers; $i++ ){
								?>
										<div class="user">
											<a href="<?php echo $users[ $i ][ 'link' ] ?>" class="desk-user"><img src="<?php echo $users[ $i ][ 'image' ] ?>"></img></a><br/>
											<a href="<?php echo $users[ $i ][ 'link' ] ?>" class="desk-username"><?php echo $users[ $i ][ 'name' ] ?></a>
										</div>
								<?php
										}
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- /.betaout -->