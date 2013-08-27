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
 * Sales orders archive grid massaction items updater
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesArchive_Model_Order_Archive_Grid_Massaction_ItemsUpdater
    extends Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater
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
            if ($this->_authorizationModel->isAllowed('Enterprise_SalesArchive::remove') === false) {
                unset($argument['remove_order_from_archive']);
            }
        }

        return $argument;
    }
}
