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
namespace Magento\Cms\Block\Widget;

class Block extends \Magento\Core\Block\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Storage for used widgets
     *
     * @var array
     */
    static protected $_widgetUsageMap = array();

    /**
     * Prepare block text and determine whether block output enabled or not
     * Prevent blocks recursion if needed
     *
     * @return \Magento\Cms\Block\Widget\Block
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
            $storeId = \Mage::app()->getStore()->getId();
            $block = \Mage::getModel('\Magento\Cms\Model\Block')
                ->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper \Magento\Cms\Helper\Data */
                $helper = \Mage::helper('Magento\Cms\Helper\Data');
                $processor = $helper->getBlockTemplateProcessor();
                $this->setText($processor->setStoreId($storeId)->filter($block->getContent()));
            }
        }

        unset(self::$_widgetUsageMap[$blockHash]);
        return $this;
    }
}
