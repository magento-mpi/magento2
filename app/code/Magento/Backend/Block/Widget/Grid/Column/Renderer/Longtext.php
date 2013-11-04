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
 * Backend Grid Renderer
 */
class Longtext extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Core string
     *
     * @var \Magento\Filter\FilterManager
     */
    protected $filter;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Filter\FilterManager $filter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Filter\FilterManager $filter,
        array $data = array()
    ) {
        $this->filter = $filter;
        parent::__construct($context, $data);
    }

    /**
     * Render contents as a long text
     *
     * Text will be truncated as specified in string_limit, truncate or 250 by default
     * Also it can be html-escaped and nl2br()
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $truncateLength = 250;
        // stringLength() is for legacy purposes
        if ($this->getColumn()->getStringLimit()) {
            $truncateLength = $this->getColumn()->getStringLimit();
        }
        if ($this->getColumn()->getTruncate()) {
            $truncateLength = $this->getColumn()->getTruncate();
        }
        $text = $this->filter->truncate(parent::_getValue($row), array('length' => $truncateLength));
        if ($this->getColumn()->getEscape()) {
            $text = $this->escapeHtml($text);
        }
        if ($this->getColumn()->getNl2br()) {
            $text = nl2br($text);
        }
        return $text;
    }
}
