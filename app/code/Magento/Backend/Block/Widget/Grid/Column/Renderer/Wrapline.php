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
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

class Wrapline
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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
     * @var \Magento\Core\Helper\String
     */
    protected $_coreString = null;

    /**
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\String $coreString,
        \Magento\Backend\Block\Context $context,
        array $data = array()
    ) {
        $this->_coreString = $coreString;
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
