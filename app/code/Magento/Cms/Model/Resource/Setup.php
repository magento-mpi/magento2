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
class Magento_Cms_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup_Generic
{
    /**
     * Block factory
     *
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * Page factory
     *
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Cms_Model_BlockFactory $blockFactory,
        Magento_Cms_Model_PageFactory $pageFactory,
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
     * @return Magento_Cms_Model_Block
     */
    public function createBlock()
    {
        return $this->_blockFactory->create();
    }

    /**
     * Create page
     *
     * @return Magento_Cms_Model_Page
     */
    public function createPage()
    {
        return $this->_pageFactory->create();
    }
}
