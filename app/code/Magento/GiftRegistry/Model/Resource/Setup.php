<?php
/**
 * Gift registry resource setup
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\GiftRegistry\Model\TypeFactory
     */
    protected $_typeFactory;

    /**
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\GiftRegistry\Model\TypeFactory $typeFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\App\ConfigInterface $config,
        \Magento\GiftRegistry\Model\TypeFactory $typeFactory,
        $moduleName = 'Magento_GiftRegistry',
        $connectionName = ''
    ) {
        $this->_typeFactory = $typeFactory;
        parent::__construct(
            $context, $resourceName, $cache, $attrGroupCollectionFactory, $config, $moduleName, $connectionName
        );
    }
}
