<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Model\Resource\Price;

/**
 * Product alert for changed price collection
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define price collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\ProductAlert\Model\Price', 'Magento\ProductAlert\Model\Resource\Price');
    }

    /**
     * Add customer filter
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $condition = $this->getConnection()->quoteInto('customer_id=?', $customerId);
        $this->addFilter('customer_id', $condition, 'string');
        return $this;
    }

    /**
     * Add website filter
     *
     * @param mixed $website
     * @return $this
     */
    public function addWebsiteFilter($website)
    {
        if (is_null($website) || $website == 0) {
            return $this;
        }
        if (is_array($website)) {
            $condition = $this->getConnection()->quoteInto('website_id IN(?)', $website);
        } elseif ($website instanceof \Magento\Core\Model\Website) {
            $condition = $this->getConnection()->quoteInto('website_id=?', $website->getId());
        } else {
            $condition = $this->getConnection()->quoteInto('website_id=?', $website);
        }
        $this->addFilter('website_id', $condition, 'string');
        return $this;
    }

    /**
     * Set order by customer
     *
     * @param string $sort
     * @return $this
     */
    public function setCustomerOrder($sort = 'ASC')
    {
        $this->getSelect()->order('customer_id ' . $sort);
        return $this;
    }
}
