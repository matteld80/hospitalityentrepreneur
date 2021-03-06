<?php      

	defined('C5_EXECUTE') or die(_("Access Denied."));
	class ProblogListBlockController extends BlockController {

		protected $btTable = 'btProBlogList';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "460";

		protected $btCacheBlockRecord = true;
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("List News Items based on type, area, or category.");
		}
		
		public function getBlockTypeName() {
			return t("ProBlog List");
		}
		
		public function getJavaScriptStrings() {
			return array(
				'feed-name' => t('Please give your RSS Feed a name.')
			);
		}
		
		function getPages($query = null) {
			$b = Block::getByID($this->bID);
			$exclude_list = array(
				'tags',
				'categories',
				'tag_cloud',
				'archive'
			);
			$template = strtolower($b->getBlockFilename());
			if(!in_array($template,$exclude_list)){
				Loader::model('problog_list','problog');
				$pl = new ProblogList();
				$db = Loader::db();
				$bID = $this->bID;
				if ($this->bID) {
					$q = "select * from btProBlogList where bID = '$bID'";
					$r = $db->query($q);
					if ($r) {
						$row = $r->fetchRow();
					}
				} else {
					$row['num'] = $this->num;
					$row['cParentID'] = $this->cParentID;
					$row['cThis'] = $this->cThis;
					$row['orderBy'] = $this->orderBy;
					$row['ctID'] = $this->ctID;
					$row['rss'] = $this->rss;
					$row['category'] = $this->category;
					$row['title'] = $this->title;
					$row['paginate'] = $this->paginate;
					$row['displayAliases'] = $this->displayAliases;
					$row['subscribe'] = $this->subscribe;
					$row['rssTitle'] = $this->rssTitle;
					$row['rssDescription'] = $this->rssDescription;
					$row['truncateSummaries'] = $this->truncateSummaries;
					$row['truncateChars'] = $this->truncateChars;
					$row['use_content'] = $this->use_content;
					$row['author'] = $this->author;
				}
	
				$pl->setNameSpace('b' . $this->bID);
				
				$cArray = array();
	
				switch($row['orderBy']) {
					case 'display_asc':
						$pl->sortByDisplayOrder();
						break;
					case 'display_desc':
						$pl->sortByDisplayOrderDescending();
						break;
					case 'chrono_asc':
						$pl->sortByPublicDateTime();
						break;
					case 'alpha_asc':
						$pl->sortByName();
						break;
					case 'alpha_desc':
						$pl->sortByNameDescending();
						break;
					default:
						$pl->sortByPublicDateDescending();
						break;
				}
	
				$num = (int) $row['num'];
				
				if ($num > 0) {
					$pl->setItemsPerPage($num);
				}
	
				$c = Page::getCurrentPage();
				if (is_object($c)) {
					$this->cID = $c->getCollectionID();
				}
				$cParentID = ($row['cThis']) ? $this->cID : $row['cParentID'];
				
				$pl->filter(false,"(CHAR_LENGTH(cv.cvName) > 4 OR cv.cvName NOT REGEXP '^[0-9]')");//filter out date pages
				
				$pl->filterByPublicDate(date('Y-m-d H:i:s'),'<=');//filter by publish date
				
				Loader::model('attribute/categories/collection');
				if ($this->displayFeaturedOnly == 1) {
					$cak = CollectionAttributeKey::getByHandle('is_featured');
					if (is_object($cak)) {
						$pl->filterByIsFeatured(1);
					}
				}
				
				if (!$row['displayAliases']) {
					$pl->filterByIsAlias(0);
				}
				$pl->filter('cvName', '', '!=');			
			
				if ($row['ctID']) {
					$pl->filterByCollectionTypeID($row['ctID']);
				}
	
				$columns = $db->MetaColumns(CollectionAttributeKey::getIndexedSearchTable());
				if (isset($columns['AK_EXCLUDE_PAGE_LIST'])) {
					$pl->filter(false, '(ak_exclude_page_list = 0 or ak_exclude_page_list is null)');
				}
	
				if ( intval($cParentID) != 0) {
					$path = Page::getByID($cParentID)->getCollectionPath();
					$pl->filterByPath($path);
				}
				
				if ($row['author']) {
					$pl->filterByBlogAuthor($row['author']);
				}
				
				if ($this->category != t('All Categories')) {
					$selected_cat = explode(', ',$this->category);
					//$category_filter = '(';
					foreach($selected_cat as $cat){
						if($fi){$category_filter .= ' OR ';}
						$category_filter .= "ak_blog_category LIKE '%\n$cat\n%'";
						$fi++;
					}
					//$category_filter .= ')';
					$pl->filter(false,"(".$category_filter.")");
					//$pl->filterByBlogCategory($category_filter);
				}	
				
				//$pl->debug();
				if ($num > 0) {
					$pages = $pl->getPage();
				} else {
					$pages = $pl->get();
				}
				$this->set('pl', $pl);
			}
			return $pages;
		}
		
		public function view() {
		
			global $c;
			Loader::model('blogify','problog');
			$blogify = Loader::helper('blogify','problog');
			$this->set('blogify',$blogify);
			Loader::model('block_types');
		    $this->set('html',Loader::helper('html'));;
		    $this->set('texthelper',Loader::helper("text")); 
		    $this->set('uh',Loader::helper('concrete/urls'));
			$this->set('link',Loader::helper('navigation')->getLinkToCollection($c));
			$blog_settings = $blogify->getBlogSettings();
			//$searchn= Page::getByID($blog_settings['search_path']);
			$this->set('blog_settings',$blog_settings);
			$searchn = Page::getByPath('/blogsearch');
			$this->set('search',Loader::helper('navigation')->getLinkToCollection($searchn));
			
			$this->set('bt',Blocktype::getByHandle('problog_list'));
			$this->set('subscribe_link',Loader::helper('concrete/urls')->getToolsURL('subscribe.php','problog'));
			
			$u = new User();
			$user = UserInfo::getByID($u->uID);
			if($user){
				$this->set('manager',$user->getUserBlogEditor());
			}

			if ( intval($this->cParentID) > 0) {
				$this->set('pp',Page::getByID($this->cParentID));
			}
			
			$cArray = array();
			
			$b = Block::getByID($this->bID);
			$template = strtolower($b->getBlockFilename());
			if(
				substr_count($template,'categories') == 0 && 
				substr_count($template,'tag') == 0 &&
				substr_count($template,'archive') == 0 
			){
				$cArray = $this->getPages();
			}

			$nh = Loader::helper('navigation');
			$this->set('nh', $nh);

			$this->set('cArray', $cArray);
			$embedlykey = $blog_settings['embedly'];
			if($embedlykey){
				$this->addFooterItem(Loader::helper('html')->javascript('jquery.embedly.js','problog'));
				$this->addFooterItem("
<script type=\"text/javascript\">
$(document).ready(function(){
  $('.embedly').each(function(){
    var w = $(this).parent().width();
  	$(this).embedly({
  		key: '$embedlykey',
  		query: {
  			maxwidth: w,
  		},
  	});
  });
});
</script>
");
			}
		}
		
		public function add() {
			Loader::model("collection_types");
			$c = Page::getCurrentPage();
			$uh = Loader::helper('concrete/urls');
			//	echo $rssUrl;
			$this->set('c', $c);
			$this->set('uh', $uh);
			$this->set('bt', BlockType::getByHandle('problog_list'));
			$this->set('displayAliases', true);
		}
	
		public function edit() {
			$b = $this->getBlockObject();
			$bCID = $b->getBlockCollectionID();
			$bID=$b->getBlockID();
			$this->set('bID', $bID);
			$c = Page::getCurrentPage();
			if ($c->getCollectionID() != $this->cParentID && (!$this->cThis) && ($this->cParentID != 0)) { 
				$isOtherPage = true;
				$this->set('isOtherPage', true);
			}
			$uh = Loader::helper('concrete/urls');
			$this->set('uh', $uh);
			$this->set('bt', BlockType::getByHandle('problog_list'));
		}
		
		function save($args) {
			// If we've gotten to the process() function for this class, we assume that we're in
			// the clear, as far as permissions are concerned (since we check permissions at several
			// points within the dispatcher)
			$db = Loader::db();

			$bID = $this->bID;
			$c = $this->getCollectionObject();
			if (is_object($c)) {
				$this->cID = $c->getCollectionID();
			}
			
			if(!$args['category'] || !is_array($args['category'])){ $args['category']=array();}
			
			if(!in_array(t('All Categories'), $args['category']) && !empty($args['category'])){
				if(count($args['category'])>1){
					$blogCat = implode(', ',$args['category']);
				}else{
					$blogCat = $args['category'][0];
				}
			}else{
				$blogCat = t('All Categories');
			}
			
			$args['num'] = ($args['num'] > 0) ? $args['num'] : 0;
			$args['cThis'] = ($args['cParentID'] == $this->cID) ? '1' : '0';
			$args['ctID'] = $args['ctID'];
			$args['cParentID'] = ($args['cParentID'] == 'OTHER') ? $args['cParentIDValue'] : $args['cParentID'];
			$args['truncateSummaries'] = ($args['truncateSummaries']) ? '1' : '0';
			$args['displayFeaturedOnly'] = ($args['displayFeaturedOnly']) ? '1' : '0';
			$args['displayAliases'] = ($args['displayAliases']) ? '1' : '0';
			$args['truncateChars'] = intval($args['truncateChars']); 
			$args['paginate'] = intval($args['paginate']); 
			$args['category'] = $blogCat;
			$args['use_content'] = ($args['use_content']) ? '1' : '0';
			$args['author'] = $args['author'];
			$args['subscribe'] = ($args['subscribe']) ? '1' : '0';
			$args['title'] = isset($args['title']) ? $args['title'] : '';

			parent::save($args);
		
		}
		
		
		/*
		/ move to helper
		*/
		function getBlockPath($path){
			$db = Loader::db();
			$r = $db->Execute("SELECT cPath FROM PagePaths WHERE cID = '$path' ");
			while ($row = $r->FetchRow()) {
				$pIDp = $row['cPath'];
				return $pIDp ;
			}
		}
		
		public function on_page_view(){
			$html = Loader::helper('html');
			$this->addHeaderItem($html->css('google-code-prettify/sunburst.css', 'problog'));
			$this->addHeaderItem($html->javascript('google-code-prettify/prettify.js','problog'));
		}
		
		/*
		/ move to helper
		*/
		public function getRssUrl($b=null){
			$uh = Loader::helper('concrete/urls');
			$b = Block::getByID($this->bID);
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			global $c;			
			$rssUrl = $uh->getBlockTypeToolsURL($bt)."/rss?cID=".$c->getCollectionID();
			return $rssUrl;
		}
		
		/*
		/ A sudo helper method for trimming up the content on a per-item bases.
		/ much cleaner than having the same code repeated in all views.
		/ This method is here instead of the helper file because we need to utilize 
		/ a few of the blocks settings to determine how to trim each content item
		*/
		public function getContent($cobj,$blog_settings){
			$texthelper = Loader::helper("text");
			extract($blog_settings);
			$content = false;
			//if 'use content' is checked go find it for this object
			if($this->use_content > 0){
				//pull for the main area
				$block = $cobj->getBlocks('Main');
				//loop through and find a match
				foreach($block as $bi) {
					if($bi->getBlockTypeHandle()=='content' || $bi->getBlockTypeHandle()=='sb_blog_post'){
						//set the content to this blocks value
						$content = $bi->getInstance()->getContent();
					}
				}
			}else{
				//else use description
				$content = $cobj->getCollectionDescription();
			}
			//if 'use page break' is checked
			if($this->PageBreak && $breakSyntax){
				//set content to array by break point
				$tempContent = explode($breakSyntax,$content);
				//set content to the first half
				$content = $tempContent[0];
			}else{
				//strip the break point out
				$content = str_replace($breakSyntax,'',$content);
			}
			//if trunctation is enabled
			if($this->truncateSummaries){
				//trim it up
				$content = $texthelper->shorten($content,$this->truncateChars);
			}
			
			return $content;
		}
	}

?>