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
namespace Magento\Cms\Block;

class Block extends \Magento\Core\Block\AbstractBlock
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
            $storeId = \Mage::app()->getStore()->getId();
            $block = \Mage::getModel('\Magento\Cms\Model\Block')
                ->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper \Magento\Cms\Helper\Data */
                $helper = \Mage::helper('Magento\Cms\Helper\Data');
                $processor = $helper->getBlockTemplateProcessor();
                $html = $processor->setStoreId($storeId)
                    ->filter($block->getContent());
            }
        }
        return $html;
    }
}
