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
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $blockId = $this->getBlockId();
        $html = '';
        if ($blockId) {
            $storeId = Mage::app()->getStore()->getId();
            $block = Mage::getModel('Magento_Cms_Model_Block')
                ->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper Magento_Cms_Helper_Data */
                $helper = Mage::helper('Magento_Cms_Helper_Data');
                $processor = $helper->getBlockTemplateProcessor();
                $html = $processor->setStoreId($storeId)
                    ->filter($block->getContent());
            }
        }
        return $html;
    }
}
