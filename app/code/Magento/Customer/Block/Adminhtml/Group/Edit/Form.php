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
 * Adminhtml customer groups edit form
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml\Group\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Customer
     */
    protected $_taxCustomer;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * @var \Magento\Customer\Service\V1\Dto\CustomerGroupBuilder
     */
    protected $_groupBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Tax\Model\TaxClass\Source\Customer $taxCustomer
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Customer\Service\V1\Dto\CustomerGroupBuilder $groupBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Tax\Model\TaxClass\Source\Customer $taxCustomer,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Customer\Service\V1\Dto\CustomerGroupBuilder $groupBuilder,
        array $data = array()
    ) {
        $this->_taxCustomer = $taxCustomer;
        $this->_groupService = $groupService;
        $this->_groupBuilder = $groupBuilder;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        if (is_null($groupId = $this->_coreRegistry->registry('current_group_id'))) {
            $customerGroup = $this->_groupBuilder->create();
        } else {
            $customerGroup = $this->_groupService->getGroup($groupId);
        }

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Group Information')));

        $validateClass = sprintf('required-entry validate-length maximum-length-%d',
            \Magento\Customer\Service\V1\CustomerGroupServiceInterface::GROUP_CODE_MAX_LENGTH);
        $name = $fieldset->addField('customer_group_code', 'text',
            array(
                'name'  => 'code',
                'label' => __('Group Name'),
                'title' => __('Group Name'),
                'note'  => __('Maximum length must be less then %1 symbols',
                    \Magento\Customer\Service\V1\CustomerGroupServiceInterface::GROUP_CODE_MAX_LENGTH),
                'class' => $validateClass,
                'required' => true,
            )
        );

        if ($customerGroup->getId() == 0 && $customerGroup->getCode()) {
            $name->setDisabled(true);
        }

        $fieldset->addField('tax_class_id', 'select',
            array(
                'name'  => 'tax_class',
                'label' => __('Tax Class'),
                'title' => __('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $this->_taxCustomer->toOptionArray()
            )
        );

        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField('id', 'hidden',
                array(
                    'name'  => 'id',
                    'value' => $customerGroup->getId(),
                )
            );
        }

        // TODO: need to figure out how the DTOs can work with forms
        $form->addValues([
            'id'                  => $customerGroup->getId(),
            'customer_group_code' => $customerGroup->getCode(),
            'tax_class_id'        => $customerGroup->getTaxClassId(),
        ]);

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('customer/*/save'));
        $this->setForm($form);
    }
}
