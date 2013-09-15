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
     * Cms data
     *
     * @var Magento_Cms_Helper_Data
     */
    protected $_cmsData = null;

    /**
     * @param Magento_Cms_Helper_Data $cmsData
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Cms_Helper_Data $cmsData,
        Magento_Core_Block_Context $context,
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
            $storeId = Mage::app()->getStore()->getId();
            $block = Mage::getModel('Magento_Cms_Model_Block')
                ->setStoreId($storeId)
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper Magento_Cms_Helper_Data */
                $helper = $this->_cmsData;
                $processor = $helper->getBlockTemplateProcessor();
                $html = $processor->setStoreId($storeId)
                    ->filter($block->getContent());
            }
        }
        return $html;
    }
}
