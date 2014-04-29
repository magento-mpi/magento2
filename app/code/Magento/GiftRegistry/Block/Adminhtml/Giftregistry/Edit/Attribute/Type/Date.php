<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
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
            array('id' => '{{prefix}}_attribute_{{id}}_date_format', 'class' => 'select global-scope')
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
        return array(
            array('value' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT, 'label' => __('Short')),
            array('value' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM, 'label' => __('Medium')),
            array('value' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_LONG, 'label' => __('Long')),
            array('value' => \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_FULL, 'label' => __('Full'))
        );
    }
}
