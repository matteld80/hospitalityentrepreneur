<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>



    
<div class="grid-2 columns">
    <div class="panel" >
      <?php  Loader::element('profile/sidebar', array('profile'=> $profile)); ?> 
      
      
      		<?php  
			$a = new Area('Profile Side');
			$a->display($c);
			?>
      
      
    </div>
</div><!--end Grid-->

<div class="grid-7 columns">

	<div class="panel">
    
            <h1><?php echo $profile->getUserName()?></h1>
        <?php 
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach($uaks as $ua) { ?>
            <div>
                <label><?php echo tc('AttributeKeyName', $ua->getAttributeKeyName())?></label>
                <?php echo $profile->getAttribute($ua, 'displaySanitized', 'display'); ?>
            </div>
        <?php  } ?>		
        
  
		
		<?php  
			$a = new Area('Main'); 
			$a->setAttribute('profile', $profile); 
			$a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
			$a->setBlockWrapperEnd('</div>');
			$a->display($c); 
		?>
        
        
        		<?php  
			$a = new Area('Members Main');
			$a->display($c);
			?>
    
    
    
    
    
    
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

    

    
	
