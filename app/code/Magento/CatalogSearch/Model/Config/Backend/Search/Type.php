<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Search change Search Type backend model
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model\Config\Backend\Search;

class Type extends \Magento\Core\Model\Config\Value
{
    /**
     * After change Catalog Search Type process
     *
     * @return \Magento\CatalogSearch\Model\Config\Backend\Search\Type|\Magento\Core\Model\AbstractModel
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = $this->_coreConfig->getValue(
            Magento_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            \Mage::getSingleton('Magento\CatalogSearch\Model\Fulltext')->resetSearchResults();
        }

        return $this;
    }
}
