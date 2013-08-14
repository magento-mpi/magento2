<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Grid widget massaction single action item
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Massaction_Item extends Magento_Backend_Block_Widget
{

    protected $_massaction = null;

    /**
     * Set parent massaction block
     *
     * @param  Magento_Backend_Block_Widget_Grid_Massaction_Extended $massaction
     * @return Magento_Backend_Block_Widget_Grid_Massaction_Item
     */
    public function setMassaction($massaction)
    {
        $this->_massaction = $massaction;
        return $this;
    }

    /**
     * Retrive parent massaction block
     *
     * @return Magento_Backend_Block_Widget_Grid_Massaction_Extended
     */
    public function getMassaction()
    {
        return $this->_massaction;
    }

    /**
     * Set additional action block for this item
     *
     * @param string|Magento_Core_Block_Abstract $block
     * @return Magento_Backend_Block_Widget_Grid_Massaction_Item
     */
    public function setAdditionalActionBlock($block)
    {
        if (is_string($block)) {
            $block = $this->getLayout()->createBlock($block);
        } elseif (is_array($block)) {
            $block = $this->_createFromConfig($block);
        } elseif (!($block instanceof Magento_Core_Block_Abstract)) {
            Mage::throwException('Unknown block type');
        }

        $this->setChild('additional_action', $block);
        return $this;
    }

    protected function _createFromConfig(array $config)
    {
        $type = isset($config['type']) ? $config['type'] : 'default';
        switch($type) {
            default:
                $blockClass = 'Magento_Backend_Block_Widget_Grid_Massaction_Item_Additional_Default';
                break;
        }

        $block = $this->getLayout()->createBlock($blockClass);
        $block->createFromConfiguration(isset($config['type']) ? $config['config'] : $config);
        return $block;
    }

    /**
     * Retrive additional action block for this item
     *
     * @return Magento_Core_Block_Abstract
     */
    public function getAdditionalActionBlock()
    {
        return $this->getChildBlock('additional_action');
    }

    /**
     * Retrive additional action block HTML for this item
     *
     * @return string
     */
    public function getAdditionalActionBlockHtml()
    {
        return $this->getChildHtml('additional_action');
    }

}
