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
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory
     * @param \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\Resource\Setup\MigrationFactory $migrationFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory,
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        $resourceName,
        $moduleName = 'Magento_GiftWrapping',
        $connectionName = ''
    ) {
        $this->_productTypeFactory = $productTypeFactory;
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct($context, $config, $cache, $migrationFactory, $coreData,
            $resourceName, $moduleName, $connectionName
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
     * @return \Magento\Catalog\Model\Resource\Setup
     */
    public function getCatalogSetup()
    {
        return $this->_catalogSetupFactory->create(array('resourceName' => 'catalog_setup'));
    }
}
