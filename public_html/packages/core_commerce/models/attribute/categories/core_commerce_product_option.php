<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * An object that represents metadata added to products. They key object maps to the "type"
 * of metadata added to pages.
 * @author Andrew Embler <andrew@concrete5.org>
 * @package Pages
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class CoreCommerceProductOptionAttributeKey extends AttributeKey {

	public function getAttributes($orderProductID, $method = 'getValue') {
		$db = Loader::db();
		$values = $db->GetAll("select akID, avID from CoreCommerceProductOptionAttributeValues where orderProductID = ?", array($orderProductID));
		$avl = new AttributeValueList();
		foreach($values as $val) {
			$ak = CoreCommerceProductOptionAttributeKey::getByID($val['akID']);
			if (is_object($ak)) {
				$value = $ak->getAttributeValue($val['avID'], $method);
				$avl->addAttributeValue($ak, $value);
			}
		}
		return $avl;
	}
	
	public function getProductID() {return $this->productID;}
	public function getOrderProductID() {return $this->orderProductID;}
	public function getAttributeKeyHandle() {
		$akHandle = parent::getAttributeKeyHandle();
		if ($this->productID > 0) { 
			return substr($akHandle, strlen($this->productID . '_'));
		} else {
			return $akHandle;
		}
	}
	public function isProductOptionAttributeKeyRequired() {
		return $this->poakIsRequired;
	}
	
	public static function getColumnHeaderList() {
		return parent::getList('core_commerce_product_option', array('akIsColumnHeader' => 1));	
	}

	public static function getSearchableList() {
		return parent::getList('core_commerce_product_option', array('akIsSearchable' => 1));	
	}
	public static function getSearchableIndexedList() {
		return parent::getList('core_commerce_product_option', array('akIsSearchableIndexed' => 1));	
	}
	
	public function assignGlobalKeyToProduct($product) {
		$db = Loader::db();
		$db->Replace('CoreCommerceProductOptionAttributeKeys',
			array('productID' => $product->getProductID(),
			'akID' => $this->getAttributeKeyID(),
			'poakIsRequired' => $this->isProductOptionAttributeKeyRequired()),
			array('akID', 'productID'));
	}
	
	public function removeGlobalKeyFromProduct($product) {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceProductOptionAttributeKeys where akID = ? and productID = ?',
			array($this->getAttributeKeyID(), $product->getProductID()));
	}

	public function sortListByDisplayOrder($a, $b) {
		if ($a->getAttributeKeyDisplayOrder() == $b->getAttributeKeyDisplayOrder()) {
			if ($a->getAttributeKeyDisplayOrder() == 0) {
				return ($a->getAttributeKeyID() < $b->getAttributeKeyID()) ? -1 : 1;
			} else {
				return 0;
			}
		} else {
			return ($a->getAttributeKeyDisplayOrder() < $b->getAttributeKeyDisplayOrder()) ? -1 : 1;
		}
	}
	
	public function getAttributeKeyDisplayOrder() {return $this->displayOrder;}
		
	public static function updateAttributesDisplayOrder($product, $uats) {
		$db = Loader::db();
		for ($i = 0; $i < count($uats); $i++) {
			$v = array($uats[$i]);
			$db->Replace('CoreCommerceProductOptionAttributeKeys', array('productID' => $product->getProductID(), 'displayOrder' => $i, 'akID' => $uats[$i]), array('productID', 'akID'));
		}
	}
	
	public static function getRequiredList($product) {
		$tattribs = self::getList($product);
		$attribs = array();
		foreach($tattribs as $poak) {
			if (!$poak->isProductOptionAttributeKeyRequired()) {
				continue;
			}			
			$attribs[] = $poak;
		}
		unset($tattribs);
		return $attribs;
	}

	public function getAttributeValue($avID, $method = 'getValue') {
		$av = CoreCommerceProductOptionAttributeValue::getByID($avID);
		$av->setAttributeKey($this);
		return call_user_func_array(array($av, $method), array());
	}
	
	public static function getByID($akID) {
		$ak = new CoreCommerceProductOptionAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
		return $ak;
	}

	public function load($akID) {
		parent::load($akID);
		$db = Loader::db();
		$row = $db->GetRow("select productID, poakIsRequired from CoreCommerceProductOptionAttributeKeys where akID = ?", array($akID));
		$this->setPropertiesFromArray($row);
	}	

	public function duplicate($product) {
		if($this->productID == 0) {
			$this->assignGlobalKeyToProduct($product);
			return;
		}//implied else
		$args['akHandle'] = $product->getProductID() . '_' . $this->getAttributeKeyHandle();
		$newAK = parent::duplicate($args);
		$db = Loader::db();
		$db->Execute("insert into CoreCommerceProductOptionAttributeKeys (akID, productID, poakIsRequired) values (?, ?, ?)",
		  array($newAK->getAttributeKeyID(), $product->getProductID(), $this->poakIsRequired));
	}

	public static function getByHandle($akHandle) {
		$db = Loader::db();
		$akID = $db->GetOne('select akID from AttributeKeys inner join AttributeKeyCategories on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akHandle = ? and akCategoryHandle = \'core_commerce_product_option\'', array($akHandle));
		$ak = new CoreCommerceProductOptionAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
	}
	
	public static function getList($product = false) {
		$db = Loader::db();
		if (is_object($product)) {
			$akIDs = $db->GetCol('select akID from CoreCommerceProductOptionAttributeKeys where productID = ? order by displayOrder asc, akID asc', $product->getProductID());
		} else {
			$akIDs = $db->GetCol('select akID from CoreCommerceProductOptionAttributeKeys where productID = 0 order by displayOrder asc, akID asc');
		}
		$attribs = array();
		foreach($akIDs as $akID) {
			$ak = new CoreCommerceProductOptionAttributeKey();
			$ak->load($akID);
			$attribs[] = $ak;
		}
		return $attribs;
	}

	/** 
	 * Returns all options from the system entirely
	 */
	public static function getExportList() {
		$db = Loader::db();
		$akIDs = $db->GetCol("select akID from AttributeKeys inner join AttributeKeyCategories on AttributeKeys.akCategoryID = AttributeKeyCategories.akCategoryID where akCategoryHandle = 'core_commerce_product_option' order by akName asc");
		$attribs = array();
		foreach($akIDs as $akID) {
			$ak = new CoreCommerceProductOptionAttributeKey();
			$ak->load($akID);
			$attribs[] = $ak;
		}
		return $attribs;
	}
	
	protected function saveAttribute($orderProduct, $value = false) {
		$av = $orderProduct->getAttributeValueObject($this, true);
		parent::saveAttribute($av, $value);
		$db = Loader::db();
		$db->Replace('CoreCommerceProductOptionAttributeValues', array(
			'orderProductID' => $orderProduct->getOrderProductID(), 
			'akID' => $this->getAttributeKeyID(), 
			'avID' => $av->getAttributeValueID()
		), array('orderProductID', 'orderID', 'akID'));
		unset($av);
	}
	
	public function add($at, $product, $args, $pkg = false) {
		$ak = parent::add('core_commerce_product_option', $at, $args, $pkg);
		$db = Loader::db();
		$poakIsRequired = 1;
		if (!$args['poakIsRequired']) {
			$poakIsRequired = 0;
		}
		$db->Execute('insert into CoreCommerceProductOptionAttributeKeys (akID, productID, poakIsRequired) values (?, ?, ?)', array($ak->getAttributeKeyID(), $product->getProductID(), $poakIsRequired));
		return $ak;
	}

	public function addGlobal($at, $args, $pkg = false) {
		$ak = parent::add('core_commerce_product_option', $at, $args, $pkg);
		$db = Loader::db();
		$poakIsRequired = 1;
		if (!$args['poakIsRequired']) {
			$poakIsRequired = 0;
		}
		$db->Execute('insert into CoreCommerceProductOptionAttributeKeys (akID, productID, poakIsRequired) values (?, ?, ?)', array($ak->getAttributeKeyID(), 0, $poakIsRequired));
		return $ak;
	}
	
	public function update($args) {
		$ak = parent::update($args);
		$poakIsRequired = 1;
		if (!$args['poakIsRequired']) {
			$poakIsRequired = 0;
		}
		$db = Loader::db();
		$db->Execute('update CoreCommerceProductOptionAttributeKeys set poakIsRequired = ? where akID = ? and productID = ?', array($poakIsRequired, $this->getAttributeKeyID(), $this->getProductID()));
		return $ak;
	}
	
	
	public function delete($product = false) {
     // Log::addEntry('we are running this function');
		$numAttributes = 0;
		if (is_object($product)) {
			$db = Loader::db();
			$db->Execute('delete from CoreCommerceProductOptionAttributeKeys where productID  = ? and akID = ?', array($product->getProductID(), $this->getAttributeKeyID()));
			$numAttributes = $db->GetOne('select count(*) from CoreCommerceProductOptionAttributeKeys where akID = ?', array($this->getAttributeKeyID()));
		}		
		if ($numAttributes == 0) {
      //   Log::addEntry('No count of attributes');
			parent::delete();
			$db = Loader::db();
			$r = $db->Execute('delete from CoreCommerceProductOptionAttributeKeys where akID = ?', array($this->getAttributeKeyID()));
			$r = $db->Execute('select avID from CoreCommerceProductOptionAttributeValues where akID = ?', array($this->getAttributeKeyID()));
			while ($row = $r->FetchRow()) {
				$db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
			}
			$db->Execute('delete from CoreCommerceProductOptionAttributeValues where akID = ?', array($this->getAttributeKeyID()));
		}
	}

}

class CoreCommerceProductOptionAttributeValue extends AttributeValue {

	public function setProduct($product) {
		$this->orderProduct = $product;
	}
	
	public static function getByID($avID) {
		$cav = new CoreCommerceProductOptionAttributeValue();
		$cav->load($avID);
		if ($cav->getAttributeValueID() == $avID) {
			return $cav;
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceProductOptionAttributeValues where orderProductID = ? and akID = ? and avID = ?', array(
			$this->orderProduct->getOrderProductID(), 
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));
		// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
		$num = $db->GetOne('select count(avID) from CoreCommerceProductOptionAttributeValues where avID = ?', array($this->getAttributeValueID()));
		if ($num < 1) {
			parent::delete();
		}
	}
}
