<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource;

/**
 * Tax Setup Resource Model
 */
class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Catalog\Model\Resource\SetupFactory
     */
    protected $_setupFactory;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Catalog\Model\Resource\SetupFactory $setupFactory
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\App\ConfigInterface $config,
        \Magento\Catalog\Model\Resource\SetupFactory $setupFactory,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        $moduleName = 'Magento_Tax',
        $connectionName = ''
    ) {
        $this->_setupFactory = $setupFactory;
        $this->productTypeConfig = $productTypeConfig;
        parent::__construct($context, $resourceName, $cache, $attrGroupCollectionFactory, $config, $moduleName, $connectionName);
    }

    /**
     * Load Tax Table Data
     *
     * @param string $table
     * @return array
     */
    protected function _loadTableData($table)
    {
        $table = $this->getTable($table);
        $select = $this->_connection->select();
        $select->from($table);
        return $this->_connection->fetchAll($select);
    }

    /**
     * @param array $data
     * @return \Magento\Catalog\Model\Resource\Setup
     */
    public function getCatalogResourceSetup(array $data = array())
    {
        return $this->_setupFactory->create($data);
    }

    /**
     * Get taxable product types
     *
     * @return array
     */
    public function getTaxableItems()
    {
        return $this->productTypeConfig->filter('taxable');
    }
}
