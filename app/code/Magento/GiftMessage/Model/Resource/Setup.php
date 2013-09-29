<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Message resource setup
 */
namespace Magento\GiftMessage\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
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
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        $resourceName,
        $moduleName = 'Magento_GiftMessage',
        $connectionName = ''
    ) {
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct($context, $config, $cache, $migrationFactory,
            $coreData, $resourceName, $moduleName, $connectionName
        );
    }


    /**
     * Create Catalog Setup Factory for GiftMessage
     *
     * @param array $data
     * @return \Magento\Catalog\Model\Resource\Setup
     */
    public function createGiftMessageSetup(array $data = array())
    {
        return $this->_catalogSetupFactory->create($data);
    }
}
