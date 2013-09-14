<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reports Recently Viewed Products Block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Product;

class Viewed extends \Magento\Reports\Block\Product\AbstractProduct
{
    const XML_PATH_RECENTLY_VIEWED_COUNT    = 'catalog/recently_products/viewed_count';

    /**
     * Viewed Product Index model name
     *
     * @var string
     */
    protected $_indexName       = 'Magento\Reports\Model\Product\Index\Viewed';

    /**
     * Retrieve page size (count)
     *
     * @return int
     */
    public function getPageSize()
    {
        if ($this->hasData('page_size')) {
            return $this->getData('page_size');
        }
        return \Mage::getStoreConfig(self::XML_PATH_RECENTLY_VIEWED_COUNT);
    }

    /**
     * Added predefined ids support
     */
    public function getCount()
    {
        $ids = $this->getProductIds();
        if (!empty($ids)) {
            return count($ids);
        }
        return parent::getCount();
    }

    /**
     * Prepare to html
     * check has viewed products
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getCount()) {
            return '';
        }
        $this->setRecentlyViewedProducts($this->getItemsCollection());
        return parent::_toHtml();
    }
}
