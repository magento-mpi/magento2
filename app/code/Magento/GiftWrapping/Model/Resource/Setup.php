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
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory
     * @param \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory,
        \Magento\Core\Model\Config $config,
        \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory,
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        $moduleName = 'Magento_GiftWrapping',
        $connectionName = ''
    ) {
        $this->productTypeConfig = $productTypeConfig;
        $this->_productTypeFactory = $productTypeFactory;
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct($context, $resourceName, $cache, $attrGrCollFactory, $config, $moduleName, $connectionName);
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
        $output = array();
        foreach ($this->productTypeConfig->getAll() as $typeKey => $config) {
            if (!isset($config['custom_attributes']['is_real_product'])
                || $config['customAttributes']['is_real_product'] == 'true') {
                $output[] = $typeKey;
            }
        }
        return $output;
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Setup
     */
    public function getCatalogSetup()
    {
        return $this->_catalogSetupFactory->create(array('resourceName' => 'catalog_setup'));
    }
}
