<?php
/**
 * Checkout item super product options block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Checkout_Block_Cart_Item_Super extends Mage_Core_Block_Abstract 
 {
 	protected $_product = null;
 	public function setProduct($product)
 	{
        $this->_product = $product;
        return $this;
 	}
 	
 	public function getProduct()
 	{
 		return $this->_product;
 	}
 	
 	public function toHtml()
 	{
		if (!$this->_beforeToHtml()) {
			return '';
		}
 		$result = '<ul class="super-product-attributes">';
 		foreach ($this->getProduct()->getSuperProduct()->getSuperAttributes(true) as $attribute) {
 			$result.= '<li><strong>' . $attribute->getFrontend()->getLabel() . ':</strong> ';
 			if($attribute->getSourceModel()) {
 				$result.= htmlspecialchars(
                    $attribute->getSource()->getOptionText($this->getProduct()->getData($attribute->getAttributeCode()))
                );
 			} else {
 				$result.= htmlspecialchars($this->getProduct()->getData($attribute->getAttributeCode()));
 			}
 			$result.='</li>';
 		}
 		$result.='</ul>';
 		return $result;
 	}
 	
 } // Class Mage_Checkout_Block_Cart_Item_Super end