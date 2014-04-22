<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

/**
 * Backend grid item renderer line to wrap
 */
class Wrapline extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Default max length of a line at one row
     *
     * @var integer
     */
    protected $_defaultMaxLineLength = 60;

    /**
     * Magento string lib
     *
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Stdlib\String $string
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Stdlib\String $string,
        array $data = array()
    ) {
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
        $line = parent::_getValue($row);
        $wrappedLine = '';
        $lineLength = $this->getColumn()->getData(
            'lineLength'
        ) ? $this->getColumn()->getData(
            'lineLength'
        ) : $this->_defaultMaxLineLength;
        for ($i = 0,$n = floor($this->string->strlen($line) / $lineLength); $i <= $n; $i++) {
            $wrappedLine .= $this->string->substr($line, $lineLength * $i, $lineLength) . "<br />";
        }
        return $wrappedLine;
    }
}
