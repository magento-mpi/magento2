<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms block content block
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Block_Block extends Magento_Core_Block_Abstract
{
    /**
     * @var Magento_Cms_Model_Template_FilterProvider
     */
    protected $_filterProvider;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Block factory
     *
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * Construct
     * 
     * @param Magento_Core_Block_Context $context
     * @param Magento_Cms_Model_Template_FilterProvider $filterProvider
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Cms_Model_BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Context $context,
        Magento_Cms_Model_Template_FilterProvider $filterProvider,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Cms_Model_BlockFactory $blockFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_blockFactory = $blockFactory;
    }

    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $blockId = $this->getBlockId();
        $html = '';
        if ($blockId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var Magento_Cms_Model_Block $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                $html = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
            }
        }
        return $html;
    }
}
