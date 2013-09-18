<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Action Special Product Attributes Condition Model
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
namespace Magento\TargetRule\Model\Actions\Condition\Product;

class Special
    extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Set condition type and value
     *
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Rule\Model\Condition\Context $context,
        array $data = array()
    ) {
        parent::__construct($backendData, $context, $data);
        $this->setType('Magento\TargetRule\Model\Actions\Condition\Product\Special');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array(
                'value' => 'Magento\TargetRule\Model\Actions\Condition\Product\Special\Price',
                'label' => __('Price (percentage)')
            )
        );

        return array(
            'value' => $conditions,
            'label' => __('Product Special')
        );
    }
}
