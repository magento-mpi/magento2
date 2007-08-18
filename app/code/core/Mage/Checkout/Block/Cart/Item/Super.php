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
 	protected $_superProduct = null;
 	public function setSuperProduct($superProduct)
 	{
 		$this->_superProduct = $superProduct;
 		return $this;
 	}
 	
 	public function getSuperProduct()
 	{
 		return $this->_superProduct;
 	}
 	
 	public function toHtml()
 	{
		if (!$this->_beforeToHtml()) {
			return '';
		}
 		$result = '<ul class="super-product-attributes">';
 		foreach ($this->getSuperProduct()->getParentProduct()->getSuperAttributes(true) as $attribute) {
 			$result.= '<li><strong>' . $attribute->getFrontend()->getLabel() . ':</strong> ';
 			if($attribute->getSourceModel()) {
 				$result.= htmlspecialchars(
								$attribute->getSource()->getOptionText(
									$this->getSuperProduct()->getData($attribute->getAttributeCode())
						   		)
						  );
 			} else {
 				$result.= htmlspecialchars(
 								$this->getSuperProduct()->getData($attribute->getAttributeCode())
 						  );
 			}
 			$result.='</li>';
 		}
 		$result.='</ul>';
 		return $result;
 	}
 	
 } // Class Mage_Checkout_Block_Cart_Item_Super end