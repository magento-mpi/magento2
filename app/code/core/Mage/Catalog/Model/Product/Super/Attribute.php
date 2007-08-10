<?php
/**
 * Catalog super product attribute model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Product_Super_Attribute extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('catalog/product_super_attribute');
	}
}// Class Mage_Catalog_Model_Product_Super_Attribute END