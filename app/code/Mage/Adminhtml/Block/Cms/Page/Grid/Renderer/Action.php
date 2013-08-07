<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Cms_Page_Grid_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Magento_Object $row)
    {
        $urlModel = Mage::getModel('Mage_Core_Model_Url')->setStore($row->getData('_first_store_id'));
        $href = $urlModel->getUrl(
            $row->getIdentifier(), array(
                '_current' => false,
                '_query' => '___store='.$row->getStoreCode()
           )
        );
        return '<a href="'.$href.'" target="_blank">'.$this->__('Preview').'</a>';
    }
}
