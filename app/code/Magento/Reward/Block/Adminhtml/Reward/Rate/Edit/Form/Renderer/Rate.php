<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate form field (element) renderer
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Adminhtml_Reward_Rate_Edit_Form_Renderer_Rate
    extends Magento_Adminhtml_Block_Template
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'rate/form/renderer/rate.phtml';

    /**
     * Return HTML
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Getter
     * Return value index in element object
     *
     * @return string
     */
    public function getValueIndex()
    {
        return $this->getElement()->getValueIndex();
    }

    /**
     * Getter
     * Return value by given value index in element object
     *
     * @return float | integer
     */
    public function getValue()
    {
        return $this->getRate()->getData($this->getValueIndex());
    }

    /**
     * Getter
     * Return equal value index in element object
     *
     * @return string
     */
    public function getEqualValueIndex()
    {
        return $this->getElement()->getEqualValueIndex();
    }

    /**
     * Return value by given equal value index in element object
     *
     * @return float | integer
     */
    public function getEqualValue()
    {
        return $this->getRate()->getData($this->getEqualValueIndex());
    }
}
