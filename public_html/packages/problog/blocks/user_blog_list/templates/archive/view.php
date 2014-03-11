<?php       
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$textHelper = Loader::helper("text"); 
	$BASE_URL = BASE_URL;
	$blogify = Loader::helper('blogify','problog');
	$searchID = $blogify->getBlogSettings();
	$searchn= Page::getByID($searchID['search_path']);
	$search= $nh->getLinkToCollection($searchn);

	if($title!=''){
		echo '<h1>'.t($title).'</h1>';
	}
	if (count($cArray) > 0) { ?>
	<div class="ccm-page-list">
	
	<?php       

	for ($i = 0; $i < count($cArray); $i++ ) {
		$cobj = $cArray[$i]; 
		if ($cobj->getCollectionDatePublic() < date('Y-m-d H:i:s') ){
		$bDate[] = $cobj->getCollectionDatePublic();
		}
	}
	$dMax=date('Y',strtotime(max($bDate)));
	$dMin=date('Y',strtotime(min($bDate)));
	$dDiff =  $dMax - $dMin;
	for($i=0; $i <= $dDiff ; $dMin=$dMin+1){
				$years[] = $dMin;
				$i++;
			}
			
	$months = array('January','February','March','April','May','June','July','August','September','October','November','December');
	$months_translated = array(t('January'),t('February'),t('March'),t('April'),t('May'),t('June'),t('July'),t('August'),t('September'),t('October'),t('November'),t('December'));
	
	//print_r($years);
	//print_r($months);
	
	foreach($years as $year){
		echo '<a onclick="javascript: $(\'#year_'.$year.'\').show(\'fast\');';
			foreach($years as $idy){
				if ($idy != $year){
		  			echo '$(\'#year_'.$idy.'\').hide(\'fast\');';
				}
			}
			echo '" style="cursor: pointer;"><h3>'.$year.'</h3></a>';
			echo '<div id="year_'.$year.'" class="arch_months" style="';
			if($year==$dMax){echo 'display: block;">';}else{echo 'display: none;">';}
			foreach($months as $key=>$month){
				echo '<a onClick="$(\'#month_'.$month.$year.'\').toggle(\'fast\')" style="cursor: pointer;">'.$months_translated[$key].'</a><br/>';
				echo '<div id="month_'.$month.$year.'"';
						if(date('Y-F')== $year.'-'.$month){
							echo 'style="display: block;">';
						}else{
							echo 'style="display: none;">';
						}
				for ($s = 0; $s < count($cArray); $s++ ) {
					$cobj = $cArray[$s]; 
					$title = $cobj->getCollectionName();
					if (date('Y-F',strtotime($cobj->getCollectionDatePublic())) == $year.'-'.$month){
						$t++;
						?>&nbsp;&nbsp;&nbsp;<a href="<?php       echo $nh->getLinkToCollection($cobj)?>"><?php       echo $title?></a><br/><?php      

					}
				}
				echo '</div>';
			}
			echo '</div>';
		}
	
	?></div><input type="hidden" id="monVal" value="1"/>

	<?php      
	
}
	

		