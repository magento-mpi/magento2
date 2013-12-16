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
 * Dashboard search query column renderer
 */
namespace Magento\Backend\Block\Dashboard\Searches\Renderer;

class Searchquery
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * String helper
     *
     * @var \Magento\Stdlib\String
     */
    protected $_stringHelper = null;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Stdlib\String $stringHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Stdlib\String $stringHelper,
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
