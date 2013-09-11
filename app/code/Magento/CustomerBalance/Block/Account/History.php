<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance history block
 *
 */
namespace Magento\CustomerBalance\Block\Account;

class History extends \Magento\Core\Block\Template
{
    /**
     * Balance history action names
     *
     * @var array
     */
    protected $_actionNames = null;

    /**
     * Check if history can be shown to customer
     *
     * @return bool
     */
    public function canShow()
    {
        return \Mage::getStoreConfigFlag('customer/magento_customerbalance/show_history');
    }

    /**
     * Retreive history events collection
     *
     * @return mixed
     */
    public function getEvents()
    {
        $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
        if (!$customerId) {
            return false;
        }

        $collection = \Mage::getModel('\Magento\CustomerBalance\Model\Balance\History')
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('website_id', \Mage::app()->getStore()->getWebsiteId())
                ->addOrder('updated_at', 'DESC')
                ->addOrder('history_id', 'DESC');

        return $collection;
    }

    /**
     * Retreive action labels
     *
     * @return array
     */
    public function getActionNames()
    {
        if (is_null($this->_actionNames)) {
            $this->_actionNames = \Mage::getSingleton('Magento\CustomerBalance\Model\Balance\History')->getActionNamesArray();
        }
        return $this->_actionNames;
    }

    /**
     * Retreive action label
     *
     * @param mixed $action
     * @return string
     */
    public function getActionLabel($action)
    {
        $names = $this->getActionNames();
        if (isset($names[$action])) {
            return $names[$action];
        }
        return '';
    }
}
