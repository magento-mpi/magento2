<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reports Recently Compared Products Block
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Block_Product_Compared extends Mage_Reports_Block_Product_Abstract
{
    const XML_PATH_RECENTLY_COMPARED_COUNT  = 'catalog/recently_products/compared_count';

    /**
     * Compared Product Index model name
     *
     * @var string
     */
    protected $_indexName       = 'Mage_Reports_Model_Product_Index_Compared';

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
        return Mage::getStoreConfig(self::XML_PATH_RECENTLY_COMPARED_COUNT);
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
