<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order\Grid\Massaction;

class ItemsUpdater implements \Magento\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(\Magento\AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Remove massaction items in case they disallowed for user
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if (false === $this->_authorization->isAllowed('Magento_Sales::cancel')) {
            unset($argument['cancel_order']);
        }

        if (false === $this->_authorization->isAllowed('Magento_Sales::hold')) {
            unset($argument['hold_order']);
        }

        if (false === $this->_authorization->isAllowed('Magento_Sales::unhold')) {
            unset($argument['unhold_order']);
        }

        return $argument;
    }
}
