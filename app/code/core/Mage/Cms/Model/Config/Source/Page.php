<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Cms_Page
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
