<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders grid massaction items updater
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Grid_Massaction_ItemsUpdater implements Magento_Core_Model_Layout_Argument_UpdaterInterface
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
