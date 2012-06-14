<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block before import form
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Block_Adminhtml_Import_Before extends Mage_Backend_Block_Template
{
    /**
     * Get JS array string of allowed customer behaviours
     *
     * @param $importVersion
     * @return string
     */
    public function getJsAllowedCustomerBehaviours($importVersion)
    {
        /** @var $helper Mage_ImportExport_Helper_Data */
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');
        $allowedBehaviours = $helper->getAllowedCustomerBehaviours($importVersion);
        if ($allowedBehaviours) {
            return '["' . implode('", "', $allowedBehaviours) . '"]';
        } else {
            return '[]';
        }
    }
}
