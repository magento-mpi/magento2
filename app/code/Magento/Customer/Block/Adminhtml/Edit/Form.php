<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

/**
 * Adminhtml customer edit form block
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Customer Service.
     *
     * @var CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        CustomerAccountServiceInterface $customerAccountService,
        array $data = array()
    ) {
        $this->_customerAccountService = $customerAccountService;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare the form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('customer/*/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ]
            ]
        );

        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);

        if ($customerId) {
            $form->addField(
                'id',
                'hidden',
                [
                    'name' => 'customer_id',
                ]
            );
            $customer = $this->_customerAccountService->getCustomer($customerId);
            $form->setValues(\Magento\Service\DataObjectConverter::toFlatArray($customer))
                ->addValues(['customer_id' => $customerId]);
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
