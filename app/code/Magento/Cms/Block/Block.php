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
     * @param Magento_Core_Block_Context $context
     * @param Magento_Cms_Model_Template_FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Context $context,
        Magento_Cms_Model_Template_FilterProvider $filterProvider,
        array $data = array()
    ) {
        $this->_filterProvider = $filterProvider;
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
            $storeId = Mage::app()->getStore()->getId();
            $block = Mage::getModel('Magento_Cms_Model_Block')
                ->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                $html = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
            }
        }
        return $html;
    }
}
