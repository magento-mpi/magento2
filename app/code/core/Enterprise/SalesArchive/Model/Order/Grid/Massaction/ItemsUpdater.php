<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders grid massaction items updater
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        $isActive = Mage::getSingleton('Enterprise_SalesArchive_Model_Config')->isArchiveActive();
        if ($isActive && Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Enterprise_SalesArchive::add')) {
           if (!isset($argument['add_order_to_archive'])) {
               $argument['add_order_to_archive'] = array(
                   'label'=> Mage::helper('Enterprise_SalesArchive_Helper_Data')->__('Move to Archive'),
                   'url'  => '*/sales_archive/massAdd'
               );
           }
        }

        return $argument;
    }
}
