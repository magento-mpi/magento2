<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry resource setup
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\GiftRegistry\Model\TypeFactory
     */
    protected $_typeFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\GiftRegistry\Model\TypeFactory $typeFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\Core\Model\Config $config,
        \Magento\GiftRegistry\Model\TypeFactory $typeFactory,
        $moduleName = 'Magento_GiftRegistry',
        $connectionName = ''
    ) {
        $this->_typeFactory = $typeFactory;
        parent::__construct($context, $resourceName, $cache, $attrGroupCollectionFactory, $config, $moduleName, $connectionName);
    }
}
