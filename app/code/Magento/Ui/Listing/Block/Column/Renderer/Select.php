<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid select input column renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Ui\Listing\Block\Column\Renderer;

class Select extends \Magento\Ui\Listing\Block\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Ui\Listing\Block\Column\Renderer\Options\Converter
     */
    protected $_converter;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Ui\Listing\Block\Column\Renderer\Options\Converter $converter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Ui\Listing\Block\Column\Renderer\Options\Converter $converter,
        array $data = array()
    ) {
        $this->_converter = $converter;
        parent::__construct($context, $data);
    }

    /**
     * Get options from column
     *
     * @return array
     */
    protected function _getOptions()
    {
        return $this->_converter->toFlatArray($this->getColumn()->getOptions());
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $name = $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId();
        $html = '<select name="' . $this->escapeHtml($name) . '" ' . $this->getColumn()->getValidateClass() . '>';
        $value = $row->getData($this->getColumn()->getIndex());
        foreach ($this->_getOptions() as $val => $label) {
            $selected = $val == $value && !is_null($value) ? ' selected="selected"' : '';
            $html .= '<option value="' . $this->escapeHtml($val) . '"' . $selected . '>';
            $html .= $this->escapeHtml($label) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
