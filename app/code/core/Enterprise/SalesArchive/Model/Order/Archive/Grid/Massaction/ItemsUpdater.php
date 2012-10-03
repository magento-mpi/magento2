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
    implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * @var Enterprise_SalesArchive_Model_Config $_salesArchiveConfig
     */
    protected $_salesArchiveConfig;

    /**
     * @var Enterprise_SalesArchive_Model_Config $_authModel
     */
    protected $_authModel;

    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->_salesArchiveConfig = (isset($data['sales_archive_config']))?
            $data['sales_archive_config'] : Mage::getSingleton('Enterprise_SalesArchive_Model_Config');
        $this->_authModel = (isset($data['authModel']))?
            $data['authModel'] : Mage::getSingleton('Mage_Core_Model_Authorization');
    }

    /**
     * Check is module active
     *
     * @return bool
     */
    protected function _isArchiveActive()
    {
        return $this->_salesArchiveConfig->isArchiveActive();
    }

    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if ($this->_isArchiveActive()) {
            if ($this->_authModel->isAllowed('Mage_Sales::cancel') === false) {
                unset($argument['cancel_order']);
            }
            if ($this->_authModel->isAllowed('Mage_Sales::hold') === false) {
                unset($argument['hold_order']);
            }
            if ($this->_authModel->isAllowed('Mage_Sales::unhold') === false) {
                unset($argument['unhold_order']);
            }
            if ($this->_authModel->isAllowed('Enterprise_SalesArchive::remove') === false) {
                unset($argument['remove_order_from_archive']);
            }
        }

        return $argument;
    }
}
