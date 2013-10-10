<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Setup Resource Model
 */
namespace Magento\Tax\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Catalog\Model\Resource\SetupFactory
     */
    protected $_setupFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Catalog\Model\Resource\SetupFactory $setupFactory
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\Core\Model\Config $config,
        \Magento\Catalog\Model\Resource\SetupFactory $setupFactory,
        $resourceName,
        $moduleName = 'Magento_Tax',
        $connectionName = ''
    ) {
        $this->_setupFactory = $setupFactory;
        parent::__construct(
            $context, $cache, $attrGrCollFactory, $coreHelper, $config, $resourceName, $moduleName, $connectionName
        );
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
}
