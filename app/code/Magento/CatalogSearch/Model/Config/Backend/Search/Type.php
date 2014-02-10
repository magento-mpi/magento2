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

use Magento\App\ConfigInterface;
use Magento\CatalogSearch\Model\Fulltext;
use Magento\Core\Model\Config\Value;
use Magento\Model\Context;
use Magento\Core\Model\Registry;
use Magento\Core\Model\Resource\AbstractResource;
use Magento\Core\Model\StoreManagerInterface;
use Magento\Data\Collection\Db;

class Type extends Value
{
    /**
     * Catalog search fulltext
     *
     * @var Fulltext
     */
    protected $_catalogSearchFulltext;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $config
     * @param Fulltext $catalogSearchFulltext
     * @param AbstractResource $resource
     * @param Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ConfigInterface $config,
        Fulltext $catalogSearchFulltext,
        AbstractResource $resource = null,
        Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogSearchFulltext = $catalogSearchFulltext;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After change Catalog Search Type process
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = $this->_config->getValue(
            Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            $this->_catalogSearchFulltext->resetSearchResults();
        }

        return $this;
    }
}
