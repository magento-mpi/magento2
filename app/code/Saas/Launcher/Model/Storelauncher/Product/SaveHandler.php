<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Save handler for Product Tile
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Product_SaveHandler implements Saas_Launcher_Model_Tile_SaveHandler
{
    /**
     * Application instance
     *
     * @var Magento_Core_Model_App
     */
    protected $_app;

    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_Core_Model_App $app
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_Core_Model_App $app,
        Magento_ObjectManager $objectManager
    ) {
        $this->_app = $app;
        $this->_objectManager = $objectManager;
    }

    /**
     * Save product related data
     *
     * @param array $data Request data
     * @throws Saas_Launcher_Exception
     */
    public function save(array $data)
    {
        $preparedData = $this->prepareData($data);
        try {
            /** @var $product Magento_Catalog_Model_Product */
            $product = $this->_objectManager->create('Magento_Catalog_Model_Product', array())
                ->setStoreId(Magento_Core_Model_App::ADMIN_STORE_ID)
                ->setTypeId($preparedData['product']['typeId'])
                ->addData($preparedData['product'])
                ->setData('_edit_mode', true)
                ->setWebsiteIds($this->getRelatedWebsiteIds());
            $product->setAttributeSetId($product->getDefaultAttributeSetId());
            $product->validate();
            $product->save();
        } catch (Exception $e) {
            throw new Saas_Launcher_Exception('Product data is invalid: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve the list of website IDs related to product
     *
     * @return array
     */
    public function getRelatedWebsiteIds()
    {
        // For now all products created via Product Tile are associated with default website
        return array($this->_app->getStore(true)->getWebsite()->getId());
    }

    /**
     * Prepare Data for saving
     *
     * @param array $data
     * @return array
     * @throws Saas_Launcher_Exception
     */
    public function prepareData(array $data)
    {
        if (!isset($data['product']) || !is_array($data['product'])) {
            throw new Saas_Launcher_Exception('Product data is invalid.');
        }
        // prevent ID overriding
        unset($data['product'][Magento_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD]);
        // prepare product stock data
        $data['product']['stock_data'] = $this->_prepareProductStockData($data);
        // only simple or virtual product can be created via Product Tile
        $data['product']['typeId'] = isset($data['product']['is_virtual'])
            ? Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL
            : Magento_Catalog_Model_Product_Type::TYPE_SIMPLE;

        return $data;
    }

    /**
     * Prepare product stock data
     *
     * @param array $data product data
     * @return array
     */
    protected function _prepareProductStockData(array $data)
    {
        $stockData = array();
        // process 'quantity_and_stock_status' attribute
        $stockData['qty'] = (!empty($data['product']['quantity_and_stock_status']['qty']))
            ? (int)$data['product']['quantity_and_stock_status']['qty']
            : 0;
        $stockData['is_in_stock'] = empty($data['product']['quantity_and_stock_status']['is_in_stock']) ? 0 : 1;
        $stockData['manage_stock'] = (!empty($stockData['qty'])) ? 1 : 0;
        // manage stock explicitly
        $stockData['use_config_manage_stock'] = 0;
        // quantity can be represented only by integer value for products created via Product Tile
        $stockData['is_qty_decimal'] = 0;

        return $stockData;
    }
}
