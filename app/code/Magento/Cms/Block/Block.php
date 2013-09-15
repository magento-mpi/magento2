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
     * Cms data
     *
     * @var \Magento\Cms\Helper\Data
     */
    protected $_cmsData = null;

    /**
     * @param \Magento\Cms\Helper\Data $cmsData
     * @param \Magento\Core\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Helper\Data $cmsData,
        \Magento\Core\Block\Context $context,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($context, $data);
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
            $storeId = \Mage::app()->getStore()->getId();
            $block = \Mage::getModel('Magento\Cms\Model\Block')
                ->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper \Magento\Cms\Helper\Data */
                $helper = $this->_cmsData;
                $processor = $helper->getBlockTemplateProcessor();
                $html = $processor->setStoreId($storeId)
                    ->filter($block->getContent());
            }
        }
        return $html;
    }
}
