<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GiftCardAccount\Model\Pool;

abstract class AbstractPool extends \Magento\Core\Model\AbstractModel
{
    const STATUS_FREE = 0;
    const STATUS_USED = 1;

    protected $_pool_percent_used = null;
    protected $_pool_size = 0;
    protected $_pool_free_size = 0;

    /**
     * Return first free code
     * 
     * @return string
     */
    public function shift()
    {
        $notInArray = $this->getExcludedIds();
        $collection = $this->getCollection()
            ->addFieldToFilter('status', self::STATUS_FREE)
            ->setPageSize(1);
        if (is_array($notInArray) && !empty($notInArray)) {
            $collection->addFieldToFilter('code', array('nin' => $notInArray));
        }
        $collection->load();
        if (!$items = $collection->getItems()) {
            \Mage::throwException(__('No codes left in the pool.'));
        }

        $item = array_shift($items);
        return $item->getId();
    }

    /**
     * Load code pool usage info
     *
     * @return \Magento\Object
     */
    public function getPoolUsageInfo()
    {
        if (is_null($this->_pool_percent_used)) {
            $this->_pool_size = $this->getCollection()->getSize();
            $this->_pool_free_size = $this->getCollection()
                ->addFieldToFilter('status', self::STATUS_FREE)
                ->getSize();
            if (!$this->_pool_size) {
                $this->_pool_percent_used = 100;
            } else {
                $this->_pool_percent_used = 100-round($this->_pool_free_size/($this->_pool_size/100), 2);
            }
        }

        $result = new \Magento\Object();
        $result
            ->setTotal($this->_pool_size)
            ->setFree($this->_pool_free_size)
            ->setPercent($this->_pool_percent_used);
        return $result;
    }

    /**
     * Delete free codes from pool
     *
     * @return \Magento\GiftCardAccount\Model\Pool\AbstractPool
     */
    public function cleanupFree()
    {
        $this->getResource()->cleanupByStatus(self::STATUS_FREE);
        /*
        $this->getCollection()
            ->addFieldToFilter('status', self::STATUS_FREE)
            ->walk('delete');
        */
        return $this;
    }
}
