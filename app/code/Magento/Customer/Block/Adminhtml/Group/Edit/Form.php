<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Group\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Tax\Service\V1\Data\TaxClass;
use Magento\Tax\Service\V1\TaxClassServiceInterface;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\FilterBuilder;

/**
 * Adminhtml customer groups edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var TaxClassServiceInterface
     */
    protected $_taxClassService;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @var \Magento\Framework\Convert\Object
     */
    protected $_converter;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerGroupBuilder
     */
    protected $_groupBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param TaxClassServiceInterface $taxClassService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param \Magento\Framework\Convert\Object $_converter
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Customer\Service\V1\Data\CustomerGroupBuilder $groupBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        TaxClassServiceInterface $taxClassService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        \Magento\Framework\Convert\Object $_converter,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Customer\Service\V1\Data\CustomerGroupBuilder $groupBuilder,
        array $data = array()
    ) {
        $this->_taxClassService = $taxClassService;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_filterBuilder = $filterBuilder;
        $this->_converter = $_converter;
        $this->_groupService = $groupService;
        $this->_groupBuilder = $groupBuilder;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form for render
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $groupId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_GROUP_ID);
        /** @var \Magento\Customer\Service\V1\Data\CustomerGroup $customerGroup */
        if (is_null($groupId)) {
            $customerGroup = $this->_groupBuilder->create();
        } else {
            $customerGroup = $this->_groupService->getGroup($groupId);
        }

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Group Information')));

        $validateClass = sprintf(
            'required-entry validate-length maximum-length-%d',
            \Magento\Customer\Service\V1\CustomerGroupServiceInterface::GROUP_CODE_MAX_LENGTH
        );
        $name = $fieldset->addField(
            'customer_group_code',
            'text',
            array(
                'name' => 'code',
                'label' => __('Group Name'),
                'title' => __('Group Name'),
                'note' => __(
                    'Maximum length must be less then %1 symbols',
                    \Magento\Customer\Service\V1\CustomerGroupServiceInterface::GROUP_CODE_MAX_LENGTH
                ),
                'class' => $validateClass,
                'required' => true
            )
        );

        if ($customerGroup->getId() == 0 && $customerGroup->getCode()) {
            $name->setDisabled(true);
        }

        $filters[] = $this->_filterBuilder
            ->setField('class_type')
            ->setValue(TaxClass::TYPE_CUSTOMER)
            ->create();
        $this->_searchCriteriaBuilder->addFilter($filters);
        $searchCriteria = $this->_searchCriteriaBuilder->create();

        $fieldset->addField(
            'tax_class_id',
            'select',
            array(
                'name' => 'tax_class',
                'label' => __('Tax Class'),
                'title' => __('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $this->_converter->toOptionArray(
                        $this->_taxClassService
                            ->searchTaxClass($searchCriteria)
                            ->getItems(),
                        'classid',
                        'classname'
                    )
            )
        );

        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField('id', 'hidden', array('name' => 'id', 'value' => $customerGroup->getId()));
        }

        if ($this->_backendSession->getCustomerGroupData()) {
            $form->addValues($this->_backendSession->getCustomerGroupData());
            $this->_backendSession->setCustomerGroupData(null);
        } else {
            // TODO: need to figure out how the DATA can work with forms
            $form->addValues(
                array(
                    'id' => $customerGroup->getId(),
                    'customer_group_code' => $customerGroup->getCode(),
                    'tax_class_id' => $customerGroup->getTaxClassId()
                )
            );
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('customer/*/save'));
        $this->setForm($form);
    }
}
