<?php 
global $postData;

$form = Loader::helper('form');
$captcha = Loader::helper('validation/captcha');
$pkg = Package::getByHandle('discussion');
$u = new User();


if (isset($_REQUEST['cID'])) {
	$c = Page::getByID($_REQUEST['cID']);
} else {
	$c = Page::getByID($postData['cDiscussionPostID']);
}
$cnt = Loader::controller($c);


if ($_REQUEST['type'] != 1 && $postData['subject'] == '') {
	$postData['formActionForumPath'] = View::url($c->getCollectionPath(),'reply');
	$postData['subject'] = 'Re: '.$c->vObj->cvName;
} else if ($postData['subject'] == '')  {
	$postData['formActionForumPath'] = View::url($c->getCollectionPath(),'add_post');
}


if ($cnt->canPost()) {
?>
<div id="ccm-discussion-post-form">

<form method="post" id="discussion-post-form-form" action="<?php echo (isset($postData['formActionForumPath'])?$postData['formActionForumPath']:$_REQUEST['formActionForumPath'])?>" enctype="multipart/form-data" target="ccm-discussion-frame" onsubmit="return ccmDiscussion.submit($('#discussion-post-form-form').get(0))">
<?php echo $form->hidden('cDiscussionPostParentID', '0'); ?>
<?php echo $form->hidden('cDiscussionPostID', intval($postData['cDiscussionPostID']) ); ?>

<div class="ccm-error" id="ccm-discussion-post-errors">
	<div class="ccm-discussion-subject-error" style="display: none;"><?php echo t('- Enter a Subject')?></div>
	<div class="ccm-discussion-message-error" style="display: none;"><?php echo t('- Enter a Message')?></div>
</div>
<div class="ccm-discussion-field">
	<?php echo  $form->label('subject', t('Subject')); ?><br/>
	<?php echo  $form->text('subject', $postData['subject'],array('style'=>'width:100%;')); ?>
</div>
<a name="ccm-discussion-post-anchor"></a>
<div class="ccm-discussion-field">
	<?php echo  $form->label('message', t('Message')); ?>
	<?php echo  $form->textarea('message',$postData['message'], array('style' => 'width: 100%; height: 200px')); ?>
	<?php  if( DiscussionPostModel::getContentBlockTypeHandle()=='bbcode' ) {
		$uh = Loader::helper('concrete/urls');
		$bt = BlockType::getByHandle('bbcode');
		$bbcodeFolderPath=$uh->getBlockTypeAssetsURL($bt, ''); ?>

		<script type="text/javascript" src="<?php echo $bbcodeFolderPath ?>/bbeditor/ed.js"></script>
		<style type="text/css">
			#bbEditorButtons{text-align:left; margin-bottom:4px; }
			#bbEditorButtons img{margin-right:4px; }
		</style>
		<script type="text/javascript">
			bbeditorDir="<?php echo $bbcodeFolderPath ?>/";
			edToolbar('message');
		</script>
	<?php  } ?>

	<?php 

	$spellChecker=Loader::helper('spellchecker');
	$speller = $spellChecker->enabled();
	if($spellChecker->enabled() ){ ?>
		<div class="checkSpellingTrigger" style="float:right"><a onClick="SpellChecker.checkField($(this).parent().siblings('textarea.ccm-input-textarea'),this)"><?php echo t('Check Spelling')?></a></div>
	<?php  } ?>
</div>
<?php  if($postData['showTags']) { ?>
	<div class="ccm-discussion-tags">
		<?php 
		if(!isset($tags)) {
			$tags = $postData['tags'];
		}
		if(!isset($tags)) {
			$tags = CollectionAttributeKey::getByHandle(DISCUSSION_POST_TAG_HANDLE);
		}
		if($tags instanceof AttributeKey) {
			echo $tags->render('label',null,true);
			echo $tags->render('form',$postData['tagsValue'],true);
		}
		?>
	</div>
<?php  } ?>
<?php  if ($u->isRegistered() || $anonAttachments) { ?>
	<?php echo  $form->label('attachments', t('Attachments')); ?>
	<div class="ccm-discussion-attachments-wrapper">
		<?php 
		if(intval($postData['cDiscussionPostID'])){
			Loader::block('file');
			$a = new Area('Attachments');
			$discussionPost=Page::getByID(intval($postData['cDiscussionPostID']));
			$abArray = $a->getAreaBlocksArray($discussionPost);
			foreach($abArray as $b) {
				if($b->getBlockTypeHandle()!='file') continue;
				$fbc=new FileBlockController( $b ); ?>
				<div>
					<?php echo $form->hidden('retainAttachmentBIDs[]', intval($b->getBlockID()) ); ?>
					<?php echo $fbc->getLinkText() ?>
					<a href="javascript:void(0)" class="ccm-discussion-remove-attachment" onclick="ccmDiscussion.removeAttachment(this)"><?php echo t('remove')?></a>
				</div>
	       <?php  }
		} ?>
	</div>

	<div class="ccm-discussion-attachments-selector">
		<div><?php echo  $form->file('attachments[]', 'attachments'); ?> <a href="javascript:void(0)" class="ccm-discussion-remove-attachment" onclick="ccmDiscussion.removeAttachment(this)"><?php echo t('remove')?></a></div>
	</div>

	<div class="ccm-discussion-field">
		<a href="javascript:void(0)" class="ccm-discussion-add-attachment" onclick="ccmDiscussion.addAttachment()"><?php echo t('Attach a file')?></a>
	</div>
<?php  } // end if ?>
<?php  if($u->isRegistered()) {?>
	<div class="ccm-discussion-field">
		<label><?php echo  $form->checkbox('track',1, intval($postData['monitorPost']) ) ?> <?php echo t('Monitor this Post')?> <span class="ccm-discussion-field-note"><?php echo t('(receive an email each time a reply is posted)')?></span></label>
	</div>
<?php  } elseif(Loader::helper('discussion_config','discussion')->captchaRequired($cnt->getdiscussion)) { ?>
	<div class="ccm-discussion-field">
		<?php echo  $form->label('captcha', t('Please type the letters and numbers shown in the image.')); ?>
		<?php  echo $captcha->display(); ?>
		<?php  echo $captcha->showInput(); ?>
	</div>
<?php  } ?>
<div style="text-align:right;">
	<?php echo $form->submit('post', t('Post'))?>

	<div class="ccm-discussion-post-loader"><img src="<?php echo ASSETS_URL?>/images/icons/icon_header_loading.gif" /></div>
</div>

<div class="ccm-spacer">&nbsp;</div>
</form>
<div id="ccm-discussion-post-pending" style="display:none;">
	<?php  echo t('Your post has been saved and will be published after approval by the forum moderator.') ?>
</div>
</div>
<?php  } ?>
