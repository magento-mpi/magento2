<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

/**
 * Customer account form block
 */
class Newsletter extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var string
     */
    protected $_template = 'tab/newsletter.phtml';

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerAccountServiceInterface $customerAccountService,
        array $data = array()
    ) {
        $this->_subscriberFactory = $subscriberFactory;
        $this->_customerAccountService = $customerAccountService;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Initialize the form.
     *
     * @return $this
     */
    public function initForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_newsletter');
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $subscriber = $this->_subscriberFactory->create()->loadByCustomer($customerId);
        $this->_coreRegistry->register('subscriber', $subscriber);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Newsletter Information')]);

        $fieldset->addField('subscription', 'checkbox', [
                'label' => __('Subscribed to Newsletter'),
                'name'  => 'subscription'
            ]
        );

        if (!$this->_customerAccountService->canModify($customerId)) {
            $form->getElement('subscription')->setReadonly(true, true);
        }

        $form->getElement('subscription')->setIsChecked($subscriber->isSubscribed());

        $changedDate = $this->getStatusChangedDate();
        if ($changedDate) {
            $fieldset->addField('change_status_date', 'label', [
                    'label' =>
                        $subscriber->isSubscribed() ? __('Last Date Subscribed') : __('Last Date Unsubscribed'),
                    'value' => $changedDate,
                    'bold'  => true
                ]
            );
        }

        $this->setForm($form);
        return $this;
    }

    /**
     * Retrieve the date when the subscriber status changed.
     *
     * @return null|string
     */
    public function getStatusChangedDate()
    {
        $subscriber = $this->_coreRegistry->registry('subscriber');
        if ($subscriber->getChangeStatusAt()) {
            return $this->formatDate(
                $subscriber->getChangeStatusAt(), \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM, true
            );
        }

        return null;
    }

    /**
     * Prepare the layout.
     *
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()
                ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid', 'newsletter.grid')
        );
        return parent::_prepareLayout();
    }
}
