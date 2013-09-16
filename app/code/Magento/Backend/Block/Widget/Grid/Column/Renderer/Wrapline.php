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
 * Backend grid item renderer line to wrap
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Column_Renderer_Wrapline
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Default max length of a line at one row
     *
     * @var integer
     */
    protected $_defaultMaxLineLength = 60;

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

    /**
     * Renders grid column
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $line = parent::_getValue($row);
        $wrappedLine = '';
        $lineLength = $this->getColumn()->getData('lineLength')
            ? $this->getColumn()->getData('lineLength')
            : $this->_defaultMaxLineLength;
        for ($i = 0, $n = floor($this->_coreString->strlen($line) / $lineLength); $i <= $n; $i++) {
            $wrappedLine .= $this->_coreString->substr($line, ($lineLength * $i), $lineLength)
                . "<br />";
        }
        return $wrappedLine;
    }
}
