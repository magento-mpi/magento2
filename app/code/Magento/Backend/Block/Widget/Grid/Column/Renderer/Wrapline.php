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
        for ($i = 0, $n = floor(\Mage::helper('Magento\Core\Helper\String')->strlen($line) / $lineLength); $i <= $n; $i++) {
            $wrappedLine .= \Mage::helper('Magento\Core\Helper\String')->substr($line, ($lineLength * $i), $lineLength)
                . "<br />";
        }
        return $wrappedLine;
    }
}
