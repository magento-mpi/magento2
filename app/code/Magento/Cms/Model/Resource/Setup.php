<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms resource setup
 */
namespace Magento\Cms\Model\Resource;

class Setup extends \Magento\Core\Model\Resource\Setup\Generic
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

    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        $resourceName,
        $moduleName = 'Magento_Cms',
        $connectionName = ''
    ) {
        $this->_blockFactory = $blockFactory;
        $this->_pageFactory = $pageFactory;
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
}
