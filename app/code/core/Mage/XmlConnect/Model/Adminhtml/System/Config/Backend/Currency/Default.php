<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Xmlconnect system config currency model
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Adminhtml_System_Config_Backend_Currency_Default
    extends Mage_Backend_Model_Config_Backend_Currency_Default
{
    /**
     * Update all applications "updated at" parameter with current date
     *
     * @return this
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isValueChanged()) {
            Mage::getModel('Mage_XmlConnect_Model_Application')->updateAllAppsUpdatedAtParameter();
        }
        return $this;
    }
}
