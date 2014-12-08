<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Recurring payment view grid
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magento\RecurringPayment\Block\Payments
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\RecurringPayment\Model\Payment
     */
    protected $_recurringPayment;

    /**
     * Payments collection
     *
     * @var \Magento\RecurringPayment\Model\Resource\Payment\Collection
     */
    protected $_payments = null;

    /**
     * @var \Magento\RecurringPayment\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\RecurringPayment\Model\Payment $recurringPayment
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\RecurringPayment\Block\Fields $fields
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\RecurringPayment\Model\Payment $recurringPayment,
        \Magento\Framework\Registry $registry,
        \Magento\RecurringPayment\Block\Fields $fields,
        array $data = []
    ) {
        $this->_recurringPayment = $recurringPayment;
        $this->_registry = $registry;
        parent::__construct($context, $data);
        $this->_fields = $fields;
        $this->_isScopePrivate = true;
    }

    /**
     * Instantiate payments collection
     *
     * @param array|int|string $fields
     * @return void
     */
    protected function _preparePayments($fields = '*')
    {
        $this->_payments = $this->_recurringPayment->getCollection()->addFieldToFilter(
            'customer_id',
            $this->_registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        )->addFieldToSelect(
            $fields
        )->setOrder(
            'payment_id',
            'desc'
        );
    }

    /**
     * Prepare grid data
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_preparePayments(['reference_id', 'state', 'created_at', 'updated_at', 'method_code']);

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager'
        )->setCollection(
            $this->_payments
        )->setIsOutputRequired(
            false
        );
        $this->setChild('pager', $pager);

        $this->setGridColumns(
            [
                new \Magento\Framework\Object(
                    [
                        'index' => 'reference_id',
                        'title' => $this->_fields->getFieldLabel('reference_id'),
                        'is_nobr' => true,
                        'width' => 1,
                    ]
                ),
                new \Magento\Framework\Object(
                    [
                        'index' => 'state',
                        'title' => $this->_fields->getFieldLabel('state'),
                    ]
                ),
                new \Magento\Framework\Object(
                    [
                        'index' => 'created_at',
                        'title' => $this->_fields->getFieldLabel('created_at'),
                        'is_nobr' => true,
                        'width' => 1,
                        'is_amount' => true,
                    ]
                ),
                new \Magento\Framework\Object(
                    [
                        'index' => 'updated_at',
                        'title' => $this->_fields->getFieldLabel('updated_at'),
                        'is_nobr' => true,
                        'width' => 1,
                    ]
                ),
                new \Magento\Framework\Object(
                    [
                        'index' => 'method_code',
                        'title' => $this->_fields->getFieldLabel('method_code'),
                        'is_nobr' => true,
                        'width' => 1,
                    ]
                ),
            ]
        );

        $payments = [];
        $store = $this->_storeManager->getStore();
        foreach ($this->_payments as $payment) {
            $payment->setStore($store);
            $payments[] = new \Magento\Framework\Object(
                [
                    'reference_id' => $payment->getReferenceId(),
                    'reference_id_link_url' => $this->getUrl(
                        'sales/recurringPayment/view/',
                        ['payment' => $payment->getId()]
                    ),
                    'state' => $payment->renderData('state'),
                    'created_at' => $this->formatDate($payment->getData('created_at'), 'medium', true),
                    'updated_at' => $payment->getData(
                        'updated_at'
                    ) ? $this->formatDate(
                        $payment->getData('updated_at'),
                        'short',
                        true
                    ) : '',
                    'method_code' => $payment->renderData('method_code'),
                ]
            );
        }
        if ($payments) {
            $this->setGridElements($payments);
        }
    }
}
