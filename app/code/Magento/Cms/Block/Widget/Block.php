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
     * @var Magento_Filter_Template
     */
    protected $_blockTemplateFilter;

    /**
     * Storage for used widgets
     *
     * @var array
     */
    static protected $_widgetUsageMap = array();
    
    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Filter_Template $blockTemplateFilter
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Filter_Template $blockTemplateFilter,
        array $data = array()
    ) {
        $this->_blockTemplateFilter = $blockTemplateFilter;
        parent::__construct($coreData, $context, $data);
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
            $storeId = Mage::app()->getStore()->getId();
            $block = Mage::getModel('Magento_Cms_Model_Block')
                ->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                $this->setText($this->_blockTemplateFilter->setStoreId($storeId)->filter($block->getContent()));
            }
        }

        unset(self::$_widgetUsageMap[$blockHash]);
        return $this;
    }
}
