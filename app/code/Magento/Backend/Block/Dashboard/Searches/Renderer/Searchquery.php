<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Dashboard\Searches\Renderer;

/**
 * Dashboard search query column renderer
 */
class Searchquery extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * String helper
     *
     * @var \Magento\Framework\Stdlib\String
     */
    protected $stringHelper;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Stdlib\String $stringHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Stdlib\String $stringHelper,
        array $data = array()
    ) {
        $this->stringHelper = $stringHelper;
        parent::__construct($context, $data);
    }

    /**
     * Renders a column
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($this->stringHelper->strlen($value) > 30) {
            $value = '<span title="' . $this->escapeHtml(
                $value
            ) . '">' . $this->escapeHtml(
                $this->filterManager->truncate($value, array('length' => 30))
            ) . '</span>';
        } else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
