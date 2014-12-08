<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring payment view page
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Payment;

class View extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Create buttons
     * TODO: implement ACL restrictions
     * @return \Magento\RecurringPayment\Block\Adminhtml\Payment\View
     */
    protected function _prepareLayout()
    {
        $this->buttonList->add(
            'back',
            ['label' => __('Back'), 'onclick' => "setLocation('{$this->getUrl('*/*/')}')", 'class' => 'back']
        );

        $payment = $this->_coreRegistry->registry('current_recurring_payment');
        $confirmationMessage = __('Are you sure you want to do this?');

        // cancel
        if ($payment->canCancel()) {
            $url = $this->getUrl('*/*/updateState', ['payment' => $payment->getId(), 'action' => 'cancel']);
            $this->buttonList->add(
                'cancel',
                [
                    'label' => __('Cancel'),
                    'onclick' => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                    'class' => 'delete'
                ]
            );
        }

        // suspend
        if ($payment->canSuspend()) {
            $url = $this->getUrl('*/*/updateState', ['payment' => $payment->getId(), 'action' => 'suspend']);
            $this->buttonList->add(
                'suspend',
                [
                    'label' => __('Suspend'),
                    'onclick' => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                    'class' => 'delete'
                ]
            );
        }

        // activate
        if ($payment->canActivate()) {
            $url = $this->getUrl('*/*/updateState', ['payment' => $payment->getId(), 'action' => 'activate']);
            $this->buttonList->add(
                'activate',
                [
                    'label' => __('Activate'),
                    'onclick' => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                    'class' => 'add'
                ]
            );
        }

        // get update
        if ($payment->canFetchUpdate()) {
            $url = $this->getUrl('*/*/updatePayment', ['payment' => $payment->getId()]);
            $this->buttonList->add(
                'update',
                [
                    'label' => __('Get Update'),
                    'onclick' => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                    'class' => 'add'
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Set title and a hack for tabs container
     *
     * @return \Magento\RecurringPayment\Block\Adminhtml\Payment\View
     */
    protected function _beforeToHtml()
    {
        $payment = $this->_coreRegistry->registry('current_recurring_payment');
        $this->_headerText = __('Recurring Payment # %1', $payment->getReferenceId());
        $this->setViewHtml('<div id="' . $this->getDestElementId() . '"></div>');
        return parent::_beforeToHtml();
    }
}
