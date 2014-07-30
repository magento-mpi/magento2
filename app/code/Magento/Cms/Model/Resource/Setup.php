<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource;

/**
 * Cms resource setup
 */
class Setup extends \Magento\Framework\Module\Setup
{
    /**
     * Block factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Cms\Model\Resource\Page\CollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @param \Magento\Framework\Module\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Cms\Model\Resource\Page\CollectionFactory $pageCollectionFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Module\Setup\Context $context,
        $resourceName,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\Resource\Page\CollectionFactory $pageCollectionFactory,
        $moduleName = 'Magento_Cms',
        $connectionName = \Magento\Framework\Module\Updater\SetupInterface::DEFAULT_SETUP_CONNECTION
    ) {
        $this->_blockFactory = $blockFactory;
        $this->_pageFactory = $pageFactory;
        $this->pageCollectionFactory = $pageCollectionFactory;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * Create block
     *
     * @return \Magento\Cms\Model\Block
     */
    public function createBlock()
    {
        return $this->_blockFactory->create();
    }

    /**
     * Create page
     *
     * @return \Magento\Cms\Model\Page
     */
    public function createPage()
    {
        return $this->_pageFactory->create();
    }

    /**
     * @return \Magento\Cms\Model\Resource\Page\Collection
     */
    public function getPageCollection()
    {
        return $this->pageCollectionFactory->create();
    }
}
