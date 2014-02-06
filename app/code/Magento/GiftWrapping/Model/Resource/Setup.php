<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping resource setup
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
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory
     * @param \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $realProductList
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\App\ConfigInterface $config,
        \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory,
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        $moduleName = 'Magento_GiftWrapping',
        $connectionName = ''
    ) {
        $this->_productTypeFactory = $productTypeFactory;
        $this->_catalogSetupFactory = $catalogSetupFactory;
        $this->productTypeConfig = $productTypeConfig;
        parent::__construct($context, $resourceName, $cache, $attrGroupCollectionFactory, $config, $moduleName, $connectionName);
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
        return $this->_catalogSetupFactory->create(array('resourceName' => 'catalog_setup'));
    }
}
