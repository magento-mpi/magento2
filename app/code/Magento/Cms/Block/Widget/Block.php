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
 * Cms Static Block Widget
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Block_Widget_Block extends Magento_Core_Block_Template implements Magento_Widget_Block_Interface
{
    /**
     * Storage for used widgets
     *
     * @var array
     */
    static protected $_widgetUsageMap = array();

    /**
     * Cms data
     *
     * @var Magento_Cms_Helper_Data
     */
    protected $_cmsData;

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
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Cms_Helper_Data $cmsData
     * @param Magento_Cms_Model_BlockFactory $blockFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Cms_Helper_Data $cmsData,
        Magento_Cms_Model_BlockFactory $blockFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_cmsData = $cmsData;
        $this->_blockFactory = $blockFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Prepare block text and determine whether block output enabled or not
     * Prevent blocks recursion if needed
     *
     * @return Magento_Cms_Block_Widget_Block
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $blockId = $this->getData('block_id');
        $blockHash = get_class($this) . $blockId;

        if (isset(self::$_widgetUsageMap[$blockHash])) {
            return $this;
        }
        self::$_widgetUsageMap[$blockHash] = true;

        if ($blockId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var Magento_Cms_Model_Block $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper Magento_Cms_Helper_Data */
                $helper = $this->_cmsData;
                $processor = $helper->getBlockTemplateProcessor();
                $this->setText($processor->setStoreId($storeId)->filter($block->getContent()));
            }
        }

        unset(self::$_widgetUsageMap[$blockHash]);
        return $this;
    }
}
