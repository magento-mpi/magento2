<?php
/**
 * Reward resource setup model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\App\ConfigInterface $config,
        \Magento\Cms\Model\PageFactory $pageFactory,
        $moduleName = 'Magento_Reward',
        $connectionName = ''
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct(
            $context, $resourceName, $cache, $attrGroupCollectionFactory, $config, $moduleName, $connectionName
        );
    }

    /**
     * Add attribute to an entity type
     * If attribute is system will add to all existing attribute sets
     *
     * @param string|integer $entityTypeId
     * @param string $code
     * @param array $attr
     * @return \Magento\Eav\Model\Entity\Setup
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        return parent::addAttribute($entityTypeId, $code, $attr);
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }
}
