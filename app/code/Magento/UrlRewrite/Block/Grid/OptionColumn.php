<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Block\Grid;

class OptionColumn extends \Magento\Backend\Block\Widget\Grid\Column
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\UrlRewrite\Model\OptionProvider $optionProvider,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->optionProvider = $optionProvider;
    }

    /**
     * Retrieve row column field value for display
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function getRowField(\Magento\Framework\Object $row)
    {
        $renderedValue = (string)parent::getRowField($row);
        $options = $this->optionProvider->toOptionArray();
        return $options[$renderedValue];
    }
}
