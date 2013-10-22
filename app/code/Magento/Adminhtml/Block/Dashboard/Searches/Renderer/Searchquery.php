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
     * @var \Magento\Stdlib\StringIconv
     */
    protected $stringIconv;

    /**
     * Core string
     *
     * @var \Magento\Core\Helper\String
     */
    protected $_coreString;

    /**
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Stdlib\StringIconv $stringIconv
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\String $coreString,
        \Magento\Backend\Block\Context $context,
        \Magento\Stdlib\StringIconv $stringIconv,
        array $data = array()
    ) {
        $this->_coreString = $coreString;
        $this->stringIconv = $stringIconv;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($this->stringIconv->strlen($value) > 30) {
            $value = '<span title="' . $this->escapeHtml($value) . '">'
                . $this->escapeHtml($this->_coreString->truncate($value, 30)) . '</span>';
        } else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
