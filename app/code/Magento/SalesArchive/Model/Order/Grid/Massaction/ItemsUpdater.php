<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Model\Order\Grid\Massaction;

class ItemsUpdater implements \Magento\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Magento\SalesArchive\Model\Config $_salesArchiveConfig
     */
    protected $_salesArchiveConfig;

    /**
     * @var \Magento\AuthorizationInterface $_authModel
     */
    protected $_authorizationModel;

    /**
     * @param \Magento\SalesArchive\Model\Config $config
     * @param \Magento\AuthorizationInterface $authorization
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\SalesArchive\Model\Config $config,
        \Magento\AuthorizationInterface $authorization,
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
