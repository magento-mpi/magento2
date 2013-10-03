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
namespace Magento\Backend\Block\Widget\Grid\Massaction;

class Item extends \Magento\Backend\Block\Widget
{

    protected $_massaction = null;

    /**
     * Set parent massaction block
     *
     * @param  \Magento\Backend\Block\Widget\Grid\Massaction\Extended $massaction
     * @return \Magento\Backend\Block\Widget\Grid\Massaction\Item
     */
    public function setMassaction($massaction)
    {
        $this->_massaction = $massaction;
        return $this;
    }

    /**
     * Retrive parent massaction block
     *
     * @return \Magento\Backend\Block\Widget\Grid\Massaction\Extended
     */
    public function getMassaction()
    {
        return $this->_massaction;
    }

    /**
     * Set additional action block for this item
     *
     * @param string|\Magento\Core\Block\AbstractBlock $block
     * @return \Magento\Backend\Block\Widget\Grid\Massaction\Item
     * @throws \Magento\Core\Exception
     */
    public function setAdditionalActionBlock($block)
    {
        if (is_string($block)) {
            $block = $this->getLayout()->createBlock($block);
        } elseif (is_array($block)) {
            $block = $this->_createFromConfig($block);
        } elseif (!($block instanceof \Magento\Core\Block\AbstractBlock)) {
            throw new \Magento\Core\Exception('Unknown block type');
        }

        $this->setChild('additional_action', $block);
        return $this;
    }

    protected function _createFromConfig(array $config)
    {
        $type = isset($config['type']) ? $config['type'] : 'default';
        switch($type) {
            default:
                $blockClass = 'Magento\Backend\Block\Widget\Grid\Massaction\Item\Additional\DefaultAdditional';
                break;
        }

        $block = $this->getLayout()->createBlock($blockClass);
        $block->createFromConfiguration(isset($config['type']) ? $config['config'] : $config);
        return $block;
    }

    /**
     * Retrive additional action block for this item
     *
     * @return \Magento\Core\Block\AbstractBlock
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
