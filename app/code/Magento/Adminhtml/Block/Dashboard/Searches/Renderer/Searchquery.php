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
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 */
class Magento_Adminhtml_Block_Dashboard_Searches_Renderer_Searchquery
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_String $coreString,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_coreString = $coreString;
        parent::__construct($context, $data);
    }

    public function render(Magento_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($this->_coreString->strlen($value) > 30) {
            $value = '<span title="'. $this->escapeHtml($value) .'">'
                . $this->escapeHtml($this->_coreString->truncate($value, 30)) . '</span>';
        }
        else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
