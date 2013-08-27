<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cms_Model_Config_Source_Page implements Magento_Core_Model_Option_ArrayInterface
{

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Magento_Cms_Model_Resource_Page_Collection')
                ->load()->toOptionIdArray();
        }
        return $this->_options;
    }

}
