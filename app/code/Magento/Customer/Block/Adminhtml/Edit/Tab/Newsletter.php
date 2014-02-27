<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account form block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

class Newsletter extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_template = 'tab/newsletter.phtml';

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        array $data = array()
    ) {
        $this->_subscriberFactory = $subscriberFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function initForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_newsletter');
        $customer = $this->_coreRegistry->registry('current_customer');
        $subscriber = $this->_subscriberFactory->create()->loadByCustomer($customer);
        $this->_coreRegistry->register('subscriber', $subscriber);

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Newsletter Information')));

        $fieldset->addField('subscription', 'checkbox',
             array(
                    'label' => __('Subscribed to Newsletter'),
                    'name'  => 'subscription'
             )
        );

        if ($customer->isReadonly()) {
            $form->getElement('subscription')->setReadonly(true, true);
        }

        $form->getElement('subscription')->setIsChecked($subscriber->isSubscribed());

        $changedDate = $this->getStatusChangedDate();
        if ($changedDate) {
            $fieldset->addField('change_status_date', 'label', array(
                'label' => $subscriber->isSubscribed() ? __('Last Date Subscribed') : __('Last Date Unsubscribed'),
                'value' => $changedDate,
                'bold'  => true
            ));
        }

        $this->setForm($form);
        return $this;
    }

    public function getStatusChangedDate()
    {
        $subscriber = $this->_coreRegistry->registry('subscriber');
        if($subscriber->getChangeStatusAt()) {
            return $this->formatDate(
                $subscriber->getChangeStatusAt(), \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM, true
            );
        }

        return null;
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()
                ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid', 'newsletter.grid')
        );
        return parent::_prepareLayout();
    }
}
