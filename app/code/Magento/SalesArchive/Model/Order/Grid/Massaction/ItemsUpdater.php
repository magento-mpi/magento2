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
 * Sales orders grid massaction items updater
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater
    implements Magento_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * @var Magento_SalesArchive_Model_Config $_salesArchiveConfig
     */
    protected $_salesArchiveConfig;

    /**
     * @var Magento_AuthorizationInterface $_authModel
     */
    protected $_authorizationModel;

    /**
     * @param Magento_SalesArchive_Model_Config $config
     * @param Magento_AuthorizationInterface $authorization
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Magento_SalesArchive_Model_Config $config,
        Magento_AuthorizationInterface $authorization,
        $data = array()
    ) {
        $this->_salesArchiveConfig = $config;
        $this->_authorizationModel = $authorization;
    }

    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if ($this->_salesArchiveConfig->isArchiveActive()) {
            if ($this->_authorizationModel->isAllowed('Magento_SalesArchive::add') === false) {
                unset($argument['add_order_to_archive']);
            }
        }

        return $argument;
    }
}
