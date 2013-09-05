<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Search_Model_Plugin_FulltextIndexRebuild
{
    /**
     * @var Magento_Search_Helper_Data
     */
    protected $_searchHelper;

    /**
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchHelper;

    /**
     * @var Magento_Search_Model_Catalog_Layer_Filter_Price
     */
    protected $_layerFilterPrice;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * @param Magento_Search_Helper_Data $searchHelper
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchHelper
     * @param Magento_Search_Model_Catalog_Layer_Filter_Price $layerFilterPrice
     * @param Magento_Core_Model_App $app
     */
    public function __construct(
        Magento_Search_Helper_Data $searchHelper,
        Magento_CatalogSearch_Helper_Data $catalogSearchHelper,
        Magento_Search_Model_Catalog_Layer_Filter_Price $layerFilterPrice,
        Magento_Core_Model_App $app
    ) {
        $this->_searchHelper = $searchHelper;
        $this->_catalogSearchHelper = $catalogSearchHelper;
        $this->_layerFilterPrice = $layerFilterPrice;
        $this->_app = $app;
    }

    /**
     * Hold commit at indexation start if needed
     *
     * @param array $arguments
     * @return array
     */
    public function beforeRebuildIndex(array $arguments)
    {
        if ($this->_searchHelper->isThirdPartyEngineAvailable()) {

            $engine = $this->_catalogSearchHelper->getEngine();
            if ($engine->holdCommit() && is_null($arguments['productIds'])) {
                $engine->setIndexNeedsOptimization();
            }
        }

        return $arguments;
    }

    /**
     * Apply changes in search engine index.
     * Make index optimization if documents were added to index.
     * Allow commit if it was held.
     *
     * @param Magento_CatalogSearch_Model_Fulltext $result
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    public function afterRebuildIndex($result)
    {
        if ($this->_searchHelper->isThirdPartyEngineAvailable()) {

            $engine = $this->_catalogSearchHelper->getEngine();
            if ($engine->allowCommit()) {

                if ($engine->getIndexNeedsOptimization()) {
                    $engine->optimizeIndex();
                } else {
                    $engine->commitChanges();
                }

                /**
                 * Cleaning MAXPRICE cache
                 */
                $cacheTag = $this->_layerFilterPrice->getCacheTag();
                $this->_app->cleanCache(array($cacheTag));
            }
        }

        return $result;
    }
}