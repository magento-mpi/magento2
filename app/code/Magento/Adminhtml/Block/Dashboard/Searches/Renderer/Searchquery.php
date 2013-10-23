<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Dashboard\Searches\Renderer;

/**
 * Dashboard search query column renderer
 */
class Searchquery extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Magento string lib
     *
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * Filter manager
     *
     * @var \Magento\Filter\FilterManager
     */
    protected $filter;


    /**
     * @param \Magento\Filter\FilterManager $filter
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Stdlib\String $string
     * @param array $data
     */
    public function __construct(
        \Magento\Filter\FilterManager $filter,
        \Magento\Backend\Block\Context $context,
        \Magento\Stdlib\String $string,
        array $data = array()
    ) {
        $this->filter = $filter;
        $this->string = $string;
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($this->string->strlen($value) > 30) {
            $value = '<span title="' . $this->escapeHtml($value) . '">'
                . $this->escapeHtml($this->filter->truncate($value, array('length' => 30))) . '</span>';
        } else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
