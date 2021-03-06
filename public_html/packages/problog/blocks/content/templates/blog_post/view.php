<?php       
	defined('C5_EXECUTE') or die(_("Access Denied."));
	global $c;
	$blogify = Loader::helper('blogify','problog');
	extract($blogify->getBlogSettings());
	extract($blogify->getBlogVars($c));
	?>
	<div class="blog-attributes">
		<h1><?php       echo $blogTitle; ?> </h1>
	</div>
	<h4><?php       echo date('M d, Y',strtotime($blogDate));  ?></h4>
	<?php       
	//go get the content block
	$content = $blogify->closetags(str_replace($breakSyntax,'',$controller->getContent()));
	print $content;
	?>
	<div id="twee">
		<br/>
		<?php       
		//check the settings for each social link and display
		if($tweet>0){
		?>
			<span class='st_twitter_hcount' displayText='Tweet'></span>
		<?php       }
		if($fb_like==1){
		?>
			<span class='st_facebook_hcount' displayText='Facebook'></span>
		
		<?php      
		}
		if($google==1){
		?>
			<span class='st_plusone_hcount' displayText='Plusone'></span>
		<?php      
		}
		?>
			<script type="text/javascript">var switchTo5x=true;</script>
			<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
			<?php       if($sharethis){ ?>
			<script type="text/javascript">stLight.options({publisher:'<?php       echo $sharethis;?>'});</script>
			<?php       } ?>
	</div>
	<br/>
	<div class="taglist">
		<?php      echo t('Tags')?>:
		<?php       
		if(!empty($tag_list)){
			$x = 0;
			foreach($tag_list as $akct){
				if($x){echo ', ';}
				echo '<a href="'.BASE_URL.$search.str_replace(' ','_',$akct->getSelectAttributeOptionValue()).'/">'.$akct->getSelectAttributeOptionValue().'</a>';
				$x++;
					
			}
		}
		?>
	</div>
	<div class="taglist">
		<?php      echo t('Category').': '.'<a href="'.BASE_URL.$search.'categories/'.str_replace(' ','_',$cat).'/">'.$cat.'</a>';?>
	</div>
	<?php       
	if($author == 1){
		//if show author is set in settings,
		$ba = $c->getAttribute('blog_author');
		if($ba){
			$ui = UserInfo::getByID($ba);
			extract($blogify->getBlogAuthorInfo($ui));
			$uAvatar = $blogify->getPosterAvatar($ba);
		}
	?>
	<div id="bio" itemscope itemtype="http://data-vocabulary.org/Person">
		<h2><?php       echo $aboutTitle ; ?></h2>
	    <div id="bioInfo">
	    <h3><span itemprop="name"><?php       echo $firstName.' '.$lastName; ?></span></h3>
	    <h5><?php       echo $uLocation ; ?></h5>
	    	<div id="avatar"><span itemprop="photo">
	    		<?php       	echo  $uAvatar; ?>
	    	</span></div>
	    	<p>
	    	<?php       
	    	if(isset($aboutBio)){
	    		echo "<span itemprop=\"title\">" . $aboutBio . "</span>"; 
	    	}else{
	    		echo t('<i> Please add your bio info through your member profile page, or through your dashboard.</i>');
	    	}
	    	?>
	    	</p> 		
	    </div>
	</div>
	<?php  } ?>
 <br style="clear: both;"/>