<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Cms_Model_Config_Source_Page implements Magento_Core_Model_Option_ArrayInterface
{

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Mage_Cms_Model_Resource_Page_Collection')
                ->load()->toOptionIdArray();
        }
        return $this->_options;
    }

}
