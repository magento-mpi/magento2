<?php
/**
 * Catalog compare item model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Compare_Item extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('catalog/compare_item');
	}
	
	public function addCustomerData(Mage_Customer_Model_Customer $customer)
	{
		$this->setCustomerId($customer->getId());
		$this->setVisitorId(0);
		return $this;
	}
	
	public function addVisitorId($visitorId)
	{
		$this->setVisitorId($visitorId);
		$this->setCustomerId(0);
		return $this;
	}
	
	public function loadByProduct(Mage_Catalog_Model_Product $product)
	{
		$this->getResource()->loadByProduct($this,$product);		
		return $this;
	}
	
	public function addProductData(Mage_Catalog_Model_Product $product)
	{
		$this->setProductId($product->getId());
		return $this;
	}
	
	public function getDataForSave()
	{
		$data = array();
		$data['customer_id'] = $this->getCustomerId();
		$data['visitor_id']	 = $this->getVisitorId();
		$data['product_id']	 = $this->getProductId();
		
		return $data;
	}
	
}// Class Mage_Catalog_Model_Compare_Item END