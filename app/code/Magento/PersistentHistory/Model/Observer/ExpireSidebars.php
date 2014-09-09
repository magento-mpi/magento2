<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;

class ExpireSidebars
{
    /**
     * Persistent data
     *
     * @var \Magento\PersistentHistory\Helper\Data
     */
    protected $_ePersistentData = null;

    /**
     * @var \Magento\Catalog\Model\Product\Compare\Item
     */
    protected $_compareItem;

    /**
     * @var \Magento\Reports\Model\Product\Index\ComparedFactory
     */
    protected $_comparedFactory;

    /**
     * @var \Magento\Reports\Model\Product\Index\ViewedFactory
     */
    protected $_viewedFactory;

    /**
     * @param \Magento\PersistentHistory\Helper\Data $ePersistentData
     * @param \Magento\Catalog\Model\Product\Compare\Item $compareItem
     * @param \Magento\Reports\Model\Product\Index\ComparedFactory $comparedFactory
     * @param \Magento\Reports\Model\Product\Index\ViewedFactory $viewedFactory
     */
    public function __construct(
        \Magento\PersistentHistory\Helper\Data $ePersistentData,
        \Magento\Catalog\Model\Product\Compare\Item $compareItem,
        \Magento\Reports\Model\Product\Index\ComparedFactory $comparedFactory,
        \Magento\Reports\Model\Product\Index\ViewedFactory $viewedFactory
    ) {
        $this->_ePersistentData = $ePersistentData;
        $this->_compareItem = $compareItem;
        $this->_comparedFactory = $comparedFactory;
        $this->_viewedFactory = $viewedFactory;
    }

    /**
     * Expire data of Sidebars
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute($observer)
    {
        $this->_expireCompareProducts();
        $this->_expireComparedProducts();
        $this->_expireViewedProducts();
    }

    /**
     * Expire data of Compare products sidebar
     *
     * @return void
     */
    public function _expireCompareProducts()
    {
        if (!$this->_ePersistentData->isCompareProductsPersist()) {
            return;
        }
        $this->_compareItem->bindCustomerLogout();
    }

    /**
     * Expire data of Compared products sidebar
     *
     * @return void
     */
    public function _expireComparedProducts()
    {
        if (!$this->_ePersistentData->isComparedProductsPersist()) {
            return;
        }
        $this->_comparedFactory->create()->purgeVisitorByCustomer()->calculate();
    }

    /**
     * Expire data of Viewed products sidebar
     *
     * @return void
     */
    public function _expireViewedProducts()
    {
        if (!$this->_ePersistentData->isComparedProductsPersist()) {
            return;
        }
        $this->_viewedFactory->create()->purgeVisitorByCustomer()->calculate();
    }
}
