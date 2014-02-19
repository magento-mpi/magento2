<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Adminhtml\Settlement\Details;

/**
 * Settlement reports transaction details
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Paypal\Model\Report\Settlement
     */
    protected $_settlement;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Paypal\Model\Report\Settlement $settlement
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Paypal\Model\Report\Settlement $settlement,
        array $data = array()
    ) {
        $this->_settlement = $settlement;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare read-only data and group it by fieldsets
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Paypal\Model\Report\Settlement\Row */
        $model = $this->_coreRegistry->registry('current_transaction');
        $fieldsets = array(
            'reference_fieldset' => array(
                'fields' => array(
                    'transaction_id' => array('label' => $this->_settlement->getFieldLabel('transaction_id')),
                    'invoice_id' => array('label' => $this->_settlement->getFieldLabel('invoice_id')),
                    'paypal_reference_id' => array('label' => $this->_settlement->getFieldLabel('paypal_reference_id')),
                    'paypal_reference_id_type' => array(
                        'label' => $this->_settlement->getFieldLabel('paypal_reference_id_type'),
                        'value' => $model->getReferenceType($model->getData('paypal_reference_id_type'))
                    ),
                    'custom_field' => array('label' => $this->_settlement->getFieldLabel('custom_field')),
                ),
                'legend' => __('Reference Information')
            ),
            'transaction_fieldset' => array(
                'fields' => array(
                    'transaction_event_code' => array(
                        'label' => $this->_settlement->getFieldLabel('transaction_event_code'),
                        'value' => sprintf('%s (%s)',
                            $model->getData('transaction_event_code'),
                            $model->getTransactionEvent($model->getData('transaction_event_code'))
                        )
                    ),
                    'transaction_initiation_date' => array(
                        'label' => $this->_settlement->getFieldLabel('transaction_initiation_date'),
                        'value' => $this->formatDate(
                            $model->getData('transaction_initiation_date'),
                            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
                            true
                        )
                    ),
                    'transaction_completion_date' => array(
                        'label' => $this->_settlement->getFieldLabel('transaction_completion_date'),
                        'value' => $this->formatDate(
                            $model->getData('transaction_completion_date'),
                            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
                            true
                        )
                    ),
                    'transaction_debit_or_credit' => array(
                        'label' => $this->_settlement->getFieldLabel('transaction_debit_or_credit'),
                        'value' => $model->getDebitCreditText($model->getData('transaction_debit_or_credit'))
                    ),
                    'gross_transaction_amount' => array(
                        'label' => $this->_settlement->getFieldLabel('gross_transaction_amount'),
                        'value' => $this->_locale->currency($model->getData('gross_transaction_currency'))
                            ->toCurrency($model->getData('gross_transaction_amount'))
                    ),
                ),
                'legend' => __('Transaction Information')
            ),
            'fee_fieldset' => array(
                'fields' => array(
                    'fee_debit_or_credit' => array(
                        'label' => $this->_settlement->getFieldLabel('fee_debit_or_credit'),
                        'value' => $model->getDebitCreditText($model->getData('fee_debit_or_credit'))
                    ),
                    'fee_amount' => array(
                        'label' => $this->_settlement->getFieldLabel('fee_amount'),
                        'value' => $this->_locale->currency($model->getData('fee_currency'))
                            ->toCurrency($model->getData('fee_amount'))
                    ),
                ),
                'legend' => __('PayPal Fee Information')
            ),
        );

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        foreach ($fieldsets as $key => $data) {
            $fieldset = $form->addFieldset($key, array('legend' => $data['legend']));
            foreach ($data['fields'] as $id => $info) {
                $fieldset->addField($id, 'label', array(
                    'name'  => $id,
                    'label' => $info['label'],
                    'title' => $info['label'],
                    'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                ));
            }
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
