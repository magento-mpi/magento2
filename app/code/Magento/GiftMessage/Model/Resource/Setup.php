<?php
/**
 * Gift Message resource setup
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Catalog\Model\Resource\SetupFactory
     */
    protected $_catalogSetupFactory;

    /**
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\App\ConfigInterface $config,
        \Magento\Catalog\Model\Resource\SetupFactory $catalogSetupFactory,
        $moduleName = 'Magento_GiftMessage',
        $connectionName = ''
    ) {
        $this->_catalogSetupFactory = $catalogSetupFactory;
        parent::__construct(
            $context, $resourceName, $cache, $attrGroupCollectionFactory, $config, $moduleName, $connectionName
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
