<?php  

Loader::controller('/checkout');

class CheckoutShippingAddressController extends CheckoutController {
	
	public function view() {
		// test whether our cart REQUIRES shipping
		Loader::model("cart", 'core_commerce');
		
		$o = CoreCommerceCurrentOrder::get();
		if (!$o->requiresShipping()) {
			$o->clearAttribute('shipping_first_name');
			$o->clearAttribute('shipping_last_name');
			$o->clearAttribute('shipping_address');
			$o->clearAttribute('shipping_phone');
			$this->redirect($this->getNextCheckoutStep()->getRedirectURL());
		}
		
		if ($o->getAttribute('shipping_first_name') == $o->getAttribute('billing_first_name')
			&& $o->getAttribute('shipping_last_name') == $o->getAttribute('billing_last_name')
			&& $o->getAttribute('shipping_address')->__toString() == $o->getAttribute('billing_address')->__toString()
			&& $o->getAttribute('shipping_phone') == $o->getAttribute('billing_phone')
		) {
			$this->set('useBillingAddressForShipping', true);
		}
		
		$u = new User();
		if ($u->isRegistered()) {
			$ui = UserInfo::getByID($u->getUserID());
			$attr = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
			if (!$o->getOrderEmail()) {
				$o->setOrderEmail($ui->getUserEmail());
			}
			foreach($attr as $atHandle) {
				$uak = UserAttributeKey::getByHandle($atHandle);
				if (is_object($uak)) {
					$uav = $ui->getAttributeValueObject($uak);
					if (is_object($uav)) {
						$oav = $o->getAttributeValueObject(CoreCommerceOrderAttributeKey::getByHandle($atHandle));
						if (!is_object($oav)) {
							$o->setAttribute($atHandle, $uav->getValue());
						}
					}
				}
			}
		}
		
		$this->set('cart', $cart);
	}

	public function on_start() {
		parent::on_start();
		$akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
		$this->set('akHandles', $akHandles);
	}
	
	public function update_shipping_to_billing($useBilling) {
		$o = CoreCommerceCurrentOrder::get();

		if ($useBilling == 1) {
			$o->setAttribute("shipping_first_name", $o->getAttribute('billing_first_name'));
			$o->setAttribute("shipping_last_name", $o->getAttribute('billing_last_name'));
			$o->setAttribute("shipping_address", $o->getAttribute('billing_address'));
			$o->setAttribute("shipping_phone", $o->getAttribute('billing_phone'));
		} else {
			$o->clearAttribute('shipping_first_name');
			$o->clearAttribute('shipping_last_name');
			$o->clearAttribute('shipping_address');
			$o->clearAttribute('shipping_phone');
		}
		$this->redirect('/checkout/shipping/address');
	}
	
	public function submit($json=0) {
		Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
		parent::submit();

		$o = CoreCommerceCurrentOrder::get();
		
		if ($this->post('useBillingAddressForShipping')) {
			$o->setAttribute("shipping_first_name", $o->getAttribute('billing_first_name'));
			$o->setAttribute("shipping_last_name", $o->getAttribute('billing_last_name'));
			$o->setAttribute("shipping_address", $o->getAttribute('billing_address'));
			$o->setAttribute("shipping_phone", $o->getAttribute('billing_phone'));
		}
		
		/*
		$validAttributes = array(
			CoreCommerceOrderAttributeKey::getByHandle('shipping_first_name'),
			CoreCommerceOrderAttributeKey::getByHandle('shipping_last_name'),
			CoreCommerceOrderAttributeKey::getByHandle('shipping_address'),
			CoreCommerceOrderAttributeKey::getByHandle('shipping_phone')
		);
		*/
		
		// pull list of attributes in set that require validation
		$set = AttributeSet::getByHandle('core_commerce_order_shipping');
		$validAttributes = array();
		if (is_object($set)) { 
			$keys = $set->getAttributeKeys();
			foreach($keys as $eak) {
				if($eak->isOrderAttributeKeyRequired()) {
					$validAttributes[] = $eak;
				}	
			}
		}	
		
		foreach($validAttributes as $uak) {
			if (!$uak->validateAttributeForm()) {
				if ($this->post('useBillingAddressForShipping') && in_array($uak->getAttributeKeyHandle(), $this->get("akHandles"))) {
					continue;
				} else {
					$this->error->add(t('The field "%s" is required', $uak->getAttributeKeyName()));
				}
			}
		}
		
		if (!$this->error->has()) {
			//Loader::model('sales/tax/rate', 'core_commerce');
			$attributes = AttributeSet::getByHandle('core_commerce_order_shipping')->getAttributeKeys();
			foreach($attributes as $eak) {
				if ($this->post('useBillingAddressForShipping') && in_array($eak->getAttributeKeyHandle(), $this->get("akHandles"))) {
					continue;
				} else {
					$eak->saveAttributeForm($o);
				}
			}
			// save shipping info to profile
			$u = new User();
			if($this->post('save_profile') && $u->isRegistered()) {
				$ui = UserInfo::getByID($u->getUserID());
				
				$attr = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
				foreach($attr as $atHandle) {	
					$av = $o->getAttribute($atHandle);
					$uak = UserAttributeKey::getByHandle($atHandle);
					if (is_object($uak)) {
						$ui->setAttribute($uak,$av);
					}
				}
			}
			
			//this happens in checkout already so no need to do it here. it happens on every page load
			//CoreCommerceSalesTaxRate::setupEnabledRates($o);
			$ret = Events::fire('core_commerce_on_checkout_shipping_address_submit', $this);
		}
		
		if($json) {
			$result = array();
			$json = Loader::helper('json');
			
			if ($this->error->has()) {
				$result['error'] = $this->error->getList();
			} else {
				$result['success'] = 1;
			}
		
			$txt = Loader::helper('text');
			$result['nextStep'] = $txt->sanitizeFileSystem($this->getNextCheckoutStep()->getPath());
			
			echo $json->encode($result);
			exit;
		} 
		
		if(!$this->error->has()) {
			$this->redirect($this->getNextCheckoutStep()->getRedirectURL());
		}
		
	}
	
}
