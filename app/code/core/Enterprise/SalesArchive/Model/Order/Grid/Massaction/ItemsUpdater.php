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
class Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater
    implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * @var Enterprise_SalesArchive_Model_Config $_salesArchiveConfig
     */
    protected $_salesArchiveConfig;

    /**
     * @var Mage_Core_Model_Authorization $_authModel
     */
    protected $_authorizationModel;

    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->_salesArchiveConfig = isset($data['sales_archive_config']) ?
            $data['sales_archive_config'] : Mage::getSingleton('Enterprise_SalesArchive_Model_Config');
        $this->_authorizationModel = isset($data['authorizationModel']) ?
            $data['authorizationModel'] : Mage::getSingleton('Mage_Core_Model_Authorization');
    }

    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if ($this->_salesArchiveConfig->isArchiveActive()) {
            if ($this->_authorizationModel->isAllowed('Enterprise_SalesArchive::add') === false) {
                unset($argument['add_order_to_archive']);
            }
        }

        return $argument;
    }
}
