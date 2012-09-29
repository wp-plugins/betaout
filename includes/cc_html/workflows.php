<link rel='stylesheet' href="<?php echo plugins_url('cc_html/style.css', dirname(__FILE__)); ?>" type='text/css'/>

<h2><i class="icon-workflow-large"></i> Workflows</h2>
	<div class="betaout">
		<div class="row">
			<div class="column left-panel-column">
				<div class="left-panel">
					<!--<div class="left-panel-heading"></div>-->
					<div class="left-panel-nav">
					<?php
							$workflowId = isset( $_GET[ 'wid' ] ) ? $_GET[ 'wid' ] : 0;
							try{
								$workflows = ccPages::getWorkflows();
								$clientBOSiteLink = ccPages::$boSiteLink;
							}catch ( Exception $ex ){
								echo $ex->getMessage();
								exit;
							}
							$totalWorkflows = count( $workflows );
							$totalDesks = 0;
							$totalTemplates = 0;
							$workflowName = '';
							$workflowEditLink = '';
					?>
					<?php
							if( $totalWorkflows == 0 ){
					?>
								<div>No record found !</div>
					<?php
							}else{
								for( $i=0; $i < $totalWorkflows; $i++ ){
									if( $workflowId == 0 ){
										$workflowId = $workflows[ 0 ][ 'id' ];
										$totalDesks = $workflows[ 0 ][ 'totalDesks' ];
										$totalTemplates = $workflows[ 0 ][ 'totalTemplates' ];
										$workflowName = $workflows[ 0 ][ 'name' ];
										$workflowStatus = $workflows[ 0 ][ 'status' ];
										$workflowEditLink = $clientBOSiteLink . $workflows[ 0 ][ 'link' ];
									}elseif( $workflows[ $i ][ 'id' ] == $workflowId ){
										$workflowId = $workflows[ $i ][ 'id' ];
										$totalDesks = $workflows[ $i ][ 'totalDesks' ];
										$totalTemplates = $workflows[ $i ][ 'totalTemplates' ];
										$workflowName = $workflows[ $i ][ 'name' ];
										$workflowStatus = $workflows[ $i ][ 'status' ];
										$workflowEditLink = $clientBOSiteLink . $workflows[ $i ][ 'link' ];
									}
					?>
								<a class="<?php echo $workflows[ $i ][ 'id' ] == $workflowId ? 'active' : '';?>" href="<?php echo "/wp-admin/admin.php?page=workflows&wid=" . $workflows[ $i ][ 'id' ];?>"><?php echo $workflows[ $i ][ 'name' ];?></a>
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
								<b><?php echo $workflowName;?></b> <span class="grey">(<?php echo $totalDesks;?> Desks, <?php echo $totalTemplates;?> Templates)</span>
								<?php
									if(0){
										echo'<span class="fright"><i class="icon-active"></i> Active</span>';
									}
								?>
								<a class="note" href="<?php echo $workflowEditLink;?>">To Add or Edit Workflow go to Newsroom</a>
								<div class="clear"></div>
							</div>
							<div class="tab-content-body">
							<?php
								try{
									$desks = ccPages::getWorkflowDesks( $workflowId );
								}catch ( Exception $ex ){
									echo $ex->getMessage();
									exit;
								}
								$totalDesks = count( $desks );
								if( $totalDesks == 0 ){
							?>
								<div>No Desk Found !</div>
							<?php
								}else{
									for( $i = 0; $i < $totalDesks; $i++ ){
										$deskRoleId = $desks[ $i ][ 'userRoleId' ];
										if( $deskRoleId != 1 ){
											$deskBOEditLink = $clientBOSiteLink . $desks[ $i ][ 'link' ];
							?>
											<div class="desk">
												<span class="desk-number"><?php echo $i+1;?></span>
												<div class="desk-name"><a href="<?php echo $deskBOEditLink;?>"> <?php echo $desks[ $i ][ 'name' ];?> </a></div>
												<div class="user-div">
							<?php
												try{
													$users = ccPages::getDeskUsers( $desks[ $i ][ 'id' ], 11 );
												}catch ( Exception $ex ){
													echo $ex->getMessage();
													exit;
												}
												$totalUsers = count( $users );
												if($totalUsers == 0){
													echo'<div class="noDataFound">
															No Users Found.
														</div>';
												}
												for( $j = 0; $j < $totalUsers; $j++ ){
							?>
													<a class="user" href="<?php echo $users[ $j ][ 'link' ]; ?>"><img src="<?php echo $users[ $j ][ 'image' ]; ?>" title="<?php echo $users[ $j ][ 'name' ]; ?>"></img></a>
							<?php
												}
												if( $totalUsers > 10 ){
							?>
													<a href="<?php echo $deskBOEditLink;?>" class="more"><i class="icon-more"></i></a>
							<?php 				}
							?>
													<div class="clear"></div>
													<a class="text-link" href="<?php echo $deskBOEditLink;?>"><?php echo $desks[ $i ][ 'roleName' ];?></a>
													<div class="clear"></div>
												</div>
												<?php if( $totalDesks != ( $i +1 ) ){?>
												<i class="icon-arrow-right"></i>
												<?php }?>
											</div>
							<?php
										}else{
							?>
											<div class="desk">
												<span class="desk-number"><?php echo $i+1;?></span>
												<div class="desk-name"><?php echo $desks[ $i ][ 'name' ];?></div>
												<div class="user-div">
												<div class="text">
													Publish Review Desk is always Empty and only Publication Editors have access to it
												</div>
												<?php if( $totalDesks != ( $i +1 ) ){?>
												<i class="icon-arrow-right"></i>
												<?php }?>
												<div class="clear"></div>
												</div>
											</div>

							<?php
										}
									}
								}
							?>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- /.betaout -->

