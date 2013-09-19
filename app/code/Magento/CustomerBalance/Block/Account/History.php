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
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Check if history can be shown to customer
     *
     * @return bool
     */
    public function canShow()
    {
        return $this->_storeConfig->getConfigFlag('customer/magento_customerbalance/show_history');
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

        $collection = \Mage::getModel('Magento\CustomerBalance\Model\Balance\History')
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('website_id', \Mage::app()->getStore()->getWebsiteId())
                ->addOrder('updated_at', 'DESC')
                ->addOrder('history_id', 'DESC');

        return $collection;
    }

    /**
     * Retrieve action labels
     *
     * @return array
     */
    public function getActionNames()
    {
        if (is_null($this->_actionNames)) {
            $this->_actionNames = \Mage::getSingleton('Magento\CustomerBalance\Model\Balance\History')
                ->getActionNamesArray();
        }
        return $this->_actionNames;
    }

    /**
     * Retrieve action label
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
