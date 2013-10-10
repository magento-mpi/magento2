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
 * Reports Recently Compared Products Block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Product;

class Compared extends \Magento\Reports\Block\Product\AbstractProduct
{
    const XML_PATH_RECENTLY_COMPARED_COUNT  = 'catalog/recently_products/compared_count';

    /**
     * Compared Product Index type
     *
     * @var string
     */
    protected $_indexType = \Magento\Reports\Model\Product\Index\Factory::TYPE_COMPARED;

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
        return $this->_storeConfig->getConfig(self::XML_PATH_RECENTLY_COMPARED_COUNT);
    }

    /**
     * Prepare to html
     * Check has compared products
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getCount()) {
            return '';
        }

        $this->setRecentlyComparedProducts($this->getItemsCollection());

        return parent::_toHtml();
    }
}
