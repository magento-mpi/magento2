<?php
/**
 * Product form image field helper
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::getSingleton('core/store')->getConfig('catalog/images/product_upload_url')
                . $this->getValue();
        }
        return $url;
    }
    
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($attribute = $this->getEntityAttribute()) {
            if (!$attribute->getIsRequired()) {
                $html.= parent::_getDeleteCheckbox();
            }
            else {
                $html.= '<input value="'.$this->getValue().'" id="'.$this->getHtmlId().'_hidden" type="hidden" class="required-entry"/>';
                $html.= '<script type="text/javascript">
                    syncOnchangeValue(\''.$this->getHtmlId().'\', \''.$this->getHtmlId().'_hidden\');
                </script>';
            }
        }
        else {
            $html.= parent::_getDeleteCheckbox();
        }
        return $html;
    }
}
