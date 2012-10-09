<link rel='stylesheet' href="<?php echo plugins_url('cc_html/style.css', dirname(__FILE__)); ?>" type='text/css' media='all' />

<h2><i class="icon-template-large"></i> Story Templates</h2>
	<div class="betaout">
		<div class="row">
			<div class="column left-panel-column">
				<div class="left-panel">
					<!--<div class="left-panel-heading">Default Desk</div>-->
					<div class="left-panel-nav">
						<?php 
							$templateId = isset( $_GET[ 'tid' ] ) ? $_GET[ 'tid' ] : 0;
							try{
								$templates = ccPages::getTemplates();
								$clientBOSiteLink = ccPages::$boSiteLink;
							}catch ( Exception $ex ){
								echo $ex->getMessage();
								exit;
							}
							$totalTemplates = count( $templates );
							if( $totalTemplates == 0 ){
						?>
								<div>No Template found !</div>
						<?php 		
							}else{
								$templateStatus = '';
								$templateName = '';
								$templateDescription = '';
								$templateBOLink = '';
								$templateWoLink = '';
								
								for( $i=0; $i < $totalTemplates; $i++ ){
									if( $templateId == 0 ){
										$templateId = $templates[ 0 ][ 'id' ];
										$templateName = $templates[ 0 ][ 'name' ];
										$templateStatus  = $templates[ 0 ][ 'status' ];
										$templateDescription = $templates[ 0 ][ 'description' ];
										$templateBOLink = $clientBOSiteLink . $templates[ 0 ][ 'link' ];
										$templateWoLink = $clientBOSiteLink . $templates[ 0 ][ 'workflowLink' ];
										
									}elseif( $templates[ $i ][ 'id' ] == $templateId ){
										$templateName = $templates[ $i ][ 'name' ];
										$templateStatus  = $templates[ $i ][ 'status' ];
										$templateDescription = $templates[ $i ][ 'description' ];
										$templateBOLink = $clientBOSiteLink . $templates[ $i ][ 'link' ];
										$templateWoLink = $clientBOSiteLink . $templates[ $i ][ 'workflowLink' ];
									}
						?>
									<a class="<?php echo $templates[ $i ][ 'id' ] == $templateId ? 'active' : '';?>" href="<?php echo "/wp-admin/admin.php?page=templates&tid=" . $templates[ $i ][ 'id' ];?>">
										<span class="row">
											<span class="column statusCol align-middle">
											<?php if( $templates[ $i ][ 'status' ] == 'active' ){?>
												<i class="icon-active"></i>
											<?php }elseif( $templates[ $i ][ 'status' ] == 'inactive' ){?>
												<i class="icon-not-active"></i>
											<?php }elseif( $templates[ $i ][ 'status' ] == 'draft' ){?>
												<i class="icon-draft"></i>
											<?php  }?>
											</span>
											<span class="column align-middle">
												<?php echo $templates[ $i ][ 'name' ];?>
											</span>
										</span>
									</a>	
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
								<span><b><?php echo $templateName;?></b></span>
								<a class="note" href="<?php echo $templateBOLink;?>">To Add or Edit "Story Templates" go to Newsroom</a>
								<?php 
									if($templateStatus == "active"){
										echo'<span class="fright"><i class="icon-active"></i> Active</span>';
									}elseif($templateStatus == "inactive"){
										echo'<span class="fright"><i class="icon-not-active"></i> Inactive</span>';
									}elseif($templateStatus == "draft"){
										echo'<span class="fright"><i class="icon-draft"></i> Inactive</span>';
									}
								?>
								<div class="clear"></div>
							</div>
							<div class="tab-content-body">
								<div class="heading">Associated With Workflow</div>
								<!--<a class="heading" href="<?php echo $templateWoLink; ?>">Associated With Workflow</a>
							--><?php 
								try{
									$workflows = ccPages::getTemplateWorkflows( $templateId );
								}catch ( Exception $ex ){
									echo $ex->getMessage();
									exit;
								}
								$totalWorkflows = count( $workflows );
								if( $totalWorkflows == 0 ){
							?>
									<div>No record found !</div>
							<?php 		
								}else{
							?>
									<!-- <h3>Associated with <?php //echo $totalWorkflows;?> Workflow</h3> -->
									<ol class="workflows">
							<?php
									for( $i=0; $i < $totalWorkflows; $i++ ){
							?>
										<li><a href="<?php echo $templateWoLink; ?>"><?php echo $workflows[ $i ][ 'name' ];?></a></li>
							<?php 
									}
								}
								?>
								</ol>
								
							<div class="description">
								<h2 class="heading">Template Description</h2>
								<div class="content"><?php echo $templateDescription;?></div>
							</div>
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
	</div><!-- /.betaout -->