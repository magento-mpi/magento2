<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders archive grid massaction items updater
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesArchive_Model_Order_Archive_Grid_Massaction_ItemsUpdater
    extends Magento_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater
    implements Magento_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if ($this->_salesArchiveConfig->isArchiveActive()) {
            if ($this->_authorizationModel->isAllowed('Magento_Sales::cancel') === false) {
                unset($argument['cancel_order']);
            }
            if ($this->_authorizationModel->isAllowed('Magento_Sales::hold') === false) {
                unset($argument['hold_order']);
            }
            if ($this->_authorizationModel->isAllowed('Magento_Sales::unhold') === false) {
                unset($argument['unhold_order']);
            }
            if ($this->_authorizationModel->isAllowed('Magento_SalesArchive::remove') === false) {
                unset($argument['remove_order_from_archive']);
            }
        }

        return $argument;
    }
}
