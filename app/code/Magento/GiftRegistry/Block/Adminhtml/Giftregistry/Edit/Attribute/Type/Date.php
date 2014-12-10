<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Attribute\Type;

class Date extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var string
     */
    protected $_template = 'edit/type/date.phtml';

    /**
     * Select element for choosing attribute type
     *
     * @return string
     */
    public function getDateFormatSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            ['id' => '{{prefix}}_attribute_{{id}}_date_format', 'class' => 'select global-scope']
        )->setName(
            'attributes[{{prefix}}][{{id}}][date_format]'
        )->setOptions(
            $this->getDateFormatOptions()
        );

        return $select->getHtml();
    }

    /**
     * Return array of date formats
     *
     * @return array
     */
    public function getDateFormatOptions()
    {
        return [
            ['value' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT, 'label' => __('Short')],
            ['value' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM, 'label' => __('Medium')],
            ['value' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_LONG, 'label' => __('Long')],
            ['value' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_FULL, 'label' => __('Full')]
        ];
    }
}
