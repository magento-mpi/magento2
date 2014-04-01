<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Report\Filter;

/**
 * Sales Adminhtml report filter form
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Reports\Block\Adminhtml\Filter\Form
{
    /**
     * Order config
     *
     * @var \Magento\Sales\Model\Order\ConfigFactory
     */
    protected $_orderConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Sales\Model\Order\ConfigFactory $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Sales\Model\Order\ConfigFactory $orderConfig,
        array $data = array()
    ) {
        $this->_orderConfig = $orderConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Add fields to base fieldset which are general to sales reports
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var \Magento\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof \Magento\Data\Form\Element\Fieldset) {

            $statuses = $this->_orderConfig->create()->getStatuses();
            $values = array();
            foreach ($statuses as $code => $label) {
                if (false === strpos($code, 'pending')) {
                    $values[] = array('label' => __($label), 'value' => $code);
                }
            }

            $fieldset->addField(
                'show_order_statuses',
                'select',
                array(
                    'name' => 'show_order_statuses',
                    'label' => __('Order Status'),
                    'options' => array('0' => __('Any'), '1' => __('Specified')),
                    'note' => __('Applies to Any of the Specified Order Statuses')
                ),
                'to'
            );

            $fieldset->addField(
                'order_statuses',
                'multiselect',
                array('name' => 'order_statuses', 'values' => $values, 'display' => 'none'),
                'show_order_statuses'
            );

            // define field dependencies
            if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
                $this->setChild(
                    'form_after',
                    $this->getLayout()->createBlock(
                        'Magento\Backend\Block\Widget\Form\Element\Dependence'
                    )->addFieldMap(
                        "{$htmlIdPrefix}show_order_statuses",
                        'show_order_statuses'
                    )->addFieldMap(
                        "{$htmlIdPrefix}order_statuses",
                        'order_statuses'
                    )->addFieldDependence(
                        'order_statuses',
                        'show_order_statuses',
                        '1'
                    )
                );
            }
        }

        return $this;
    }
}
