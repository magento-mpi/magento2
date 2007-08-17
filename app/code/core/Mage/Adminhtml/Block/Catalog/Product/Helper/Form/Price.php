<?php
/**
 * Product form price field helper
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price extends Varien_Data_Form_Element_Text
{
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        /**
         * getEntityAttribute - use __call
         */
        if ($attribute = $this->getEntityAttribute()) {
            $storeId = null;
            if (!$attribute->getIsGlobal()) {
                $storeId = $attribute->getEntity()->getStoreId();
            }
            $currencyCode = (string) Mage::getStoreConfig('general/currency/base', $storeId);
            $html.= ' (' . __('Currency') . ' - <strong>'.$currencyCode.'</strong>)';
        }
        
        return $html;
    }
    
    public function getEscapedValue()
    {
        $value = $this->getValue();
        
        if (!is_numeric($value)) {
            return null;
        }
        
        return number_format($value, 2, null, '');
    }
}
