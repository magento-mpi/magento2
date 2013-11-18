<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Dashboard search query column renderer
 */
namespace Magento\Backend\Block\Dashboard\Searches\Renderer;

class Searchquery
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * String helper
     *
     * @var \Magento\Stdlib\String
     */
    protected $_stringHelper = null;

    /**
     * @param \Magento\Stdlib\String $stringHelper
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Stdlib\String $stringHelper,
        \Magento\Backend\Block\Context $context,
        array $data = array()
    ) {
        $this->_stringHelper = $stringHelper;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($this->_stringHelper->strlen($value) > 30) {
            $value = '<span title="'. $this->escapeHtml($value) .'">'
                . $this->escapeHtml($this->_stringHelper->truncate($value, 30)) . '</span>';
        } else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
