<?php
/**
 * Gift wrapping resource setup
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Catalog\Model\Product\TypeFactory
     */
    protected $_productTypeFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory
     * @param \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Setup\Context $context,
        $resourceName,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory,
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        $moduleName = 'Magento_GiftWrapping',
        $connectionName = \Magento\Framework\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_productTypeFactory = $productTypeFactory;
        $this->_catalogSetupFactory = $catalogSetupFactory;
        $this->productTypeConfig = $productTypeConfig;
        parent::__construct(
            $context,
            $resourceName,
            $cache,
            $attrGroupCollectionFactory,
            $config,
            $moduleName,
            $connectionName
        );
    }

    /**
     * @return \Magento\Catalog\Model\Product\Type
     */
    public function getProductType()
    {
        return $this->_productTypeFactory->create();
    }

    /**
     * Get list product types that represents real product
     *
     * @return array
     */
    public function getRealProductTypes()
    {
        return $this->productTypeConfig->filter('is_real_product');
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Setup
     */
    public function getCatalogSetup()
    {
        return $this->_catalogSetupFactory->create(['resourceName' => 'catalog_setup']);
    }
}
