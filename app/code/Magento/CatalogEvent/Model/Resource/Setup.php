<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event resource setup
 */
namespace Magento\CatalogEvent\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Cms\Model\BlockFactory $modelBlockFactory
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
        \Magento\Cms\Model\BlockFactory $modelBlockFactory,
        $resourceName,
        $moduleName = 'Magento_CatalogEvent',
        $connectionName = ''
    ) {
        $this->_blockFactory = $modelBlockFactory;
        parent::__construct(
            $context,
            $cache,
            $attrGrCollFactory,
            $coreHelper,
            $config,
            $resourceName,
            $moduleName,
            $connectionName
        );
    }

    /**
     * Get model block factory
     *
     * @return \Magento\Cms\Model\BlockFactory
     */
    public function getBlockFactory()
    {
        return $this->_blockFactory;
    }
}
