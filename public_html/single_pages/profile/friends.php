<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>



<div class="grid-2 columns">
    <div class="panel" >
      <?php  Loader::element('profile/sidebar', array('profile'=> $profile)); ?> 
    </div>
</div><!--end Grid-->

<div class="grid-7 columns">

	<div class="panel">
    
    <div id="ccm-profile-wrapper">
    
    <div id="ccm-profile-body">	
        <h1><?php echo t('My Friends') ?></h1>
        <?php 
		$friendsData = UsersFriends::getUsersFriendsData( $profile->getUserID() );
		if( !$friendsData ){ ?>
			<div style="padding:16px 0px;">
				<?php echo t('No results found.')?>
			</div>
		<?php  
		}else foreach($friendsData as $friendsData){ 
			$friendUID=$friendsData['friendUID'];
			$friendUI = UserInfo::getById( $friendUID );
			if (!is_object($friendUI)) { ?>

			<div class="ccm-users-friend" style="margin-bottom:16px;">
				<div style="float:left; width:100px;">
					<?php echo $av->outputNoAvatar()?>
				</div>
				<div >
					<?php echo t('Unknown User')?>
				</div>
				<div class="ccm-spacer"></div>
			</div>			
			
			<?php  } else { ?>
			<div class="ccm-users-friend" style="margin-bottom:16px;">
				<div style="float:left; width:100px;">
					<a href="<?php echo View::url('/profile',$friendUID)?>"><?php echo  $av->outputUserAvatar($friendUI)?></a>
				</div>
				<div >
					<a href="<?php echo View::url('/profile',$friendUID) ?>"><?php echo  $friendUI->getUsername(); ?></a>
					<div style=" font-size:90%; line-height:90%; margin-top:4px;">
					<?php echo t('Member Since') ?> <?php echo date(DATE_APP_GENERIC_MDY_FULL, strtotime($friendUI->getUserDateAdded('user')))?>
					</div>
				</div>
				<div class="ccm-spacer"></div>
			</div>			
			<?php  } ?>
		<?php  } ?>	
    </div>
	
	<div class="ccm-spacer"></div>
</div>
    
    
   
    </div>
</div><!--end Grid-->



<div class="grid-3 columns">

       <div class="panel sideMembersNav">
       <h3>Resources</h3>
        <?php  
        	$a = new GlobalArea('Resources Side Nav');
        	$a->display();
         ?>
         </div>
			
			<div class="panel">
			<?php  
			$a = new Area('Sidebar');
			$a->display($c);
			?>
            </div>



</div><!--end Grid-->




