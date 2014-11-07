<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Framework\Convert\Object as ObjectConverter;
use Magento\Store\Model\System\Store;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Shopping Cart Price Rule General Information Tab
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Framework\Convert\Object
     */
    protected $_objectConverter;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $_salesRule;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RuleFactory $salesRule
     * @param ObjectConverter $objectConverter
     * @param Store $systemStore
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleFactory $salesRule,
        ObjectConverter $objectConverter,
        Store $systemStore,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->_objectConverter = $objectConverter;
        $this->_salesRule = $salesRule;
        $this->_customerRepository = $customerRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Rule Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Rule Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_quote_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('General Information')));

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array('name' => 'rule_id'));
        }

        $fieldset->addField('product_ids', 'hidden', array('name' => 'product_ids'));

        $fieldset->addField(
            'name',
            'text',
            array('name' => 'name', 'label' => __('Rule Name'), 'title' => __('Rule Name'), 'required' => true)
        );

        $fieldset->addField(
            'description',
            'textarea',
            array(
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height: 100px;'
            )
        );

        $fieldset->addField(
            'is_active',
            'select',
            array(
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => array('1' => __('Active'), '0' => __('Inactive'))
            )
        );

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        if ($this->_storeManager->isSingleStoreMode()) {
            $websiteId = $this->_storeManager->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array('name' => 'website_ids[]', 'value' => $websiteId));
            $model->setWebsiteIds($websiteId);
        } else {
            $field = $fieldset->addField(
                'website_ids',
                'multiselect',
                array(
                    'name' => 'website_ids[]',
                    'label' => __('Websites'),
                    'title' => __('Websites'),
                    'required' => true,
                    'values' => $this->_systemStore->getWebsiteValuesForForm()
                )
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        }

        $groups = $this->_customerRepository
            ->getList($this->_searchCriteriaBuilder->create())
            ->getItems();
        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            array(
                'name' => 'customer_group_ids[]',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'required' => true,
                'values' =>  $this->_objectConverter->toOptionArray($groups, 'id', 'code')
            )
        );

        $couponTypeFiled = $fieldset->addField(
            'coupon_type',
            'select',
            array(
                'name' => 'coupon_type',
                'label' => __('Coupon'),
                'required' => true,
                'options' => $this->_salesRule->create()->getCouponTypes()
            )
        );

        $couponCodeFiled = $fieldset->addField(
            'coupon_code',
            'text',
            array('name' => 'coupon_code', 'label' => __('Coupon Code'), 'required' => true)
        );

        $autoGenerationCheckbox = $fieldset->addField(
            'use_auto_generation',
            'checkbox',
            array(
                'name' => 'use_auto_generation',
                'label' => __('Use Auto Generation'),
                'note' => __('If you select and save the rule you will be able to generate multiple coupon codes.'),
                'onclick' => 'handleCouponsTabContentActivity()',
                'checked' => (int)$model->getUseAutoGeneration() > 0 ? 'checked' : ''
            )
        );

        $autoGenerationCheckbox->setRenderer(
            $this->getLayout()->createBlock(
                'Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Main\Renderer\Checkbox'
            )
        );

        $usesPerCouponFiled = $fieldset->addField(
            'uses_per_coupon',
            'text',
            array('name' => 'uses_per_coupon', 'label' => __('Uses per Coupon'))
        );

        $fieldset->addField(
            'uses_per_customer',
            'text',
            array('name' => 'uses_per_customer',
                  'label' => __('Uses per Customer'),
                  'note' => __('Usage limit enforced for logged in customers only.')
            )
        );

        $dateFormat = $this->_localeDate->getDateFormat(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField(
            'from_date',
            'date',
            array(
                'name' => 'from_date',
                'label' => __('From Date'),
                'title' => __('From Date'),
                'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            )
        );
        $fieldset->addField(
            'to_date',
            'date',
            array(
                'name' => 'to_date',
                'label' => __('To Date'),
                'title' => __('To Date'),
                'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            )
        );

        $fieldset->addField('sort_order', 'text', array('name' => 'sort_order', 'label' => __('Priority')));

        $fieldset->addField(
            'is_rss',
            'select',
            array(
                'label' => __('Public In RSS Feed'),
                'title' => __('Public In RSS Feed'),
                'name' => 'is_rss',
                'options' => array('1' => __('Yes'), '0' => __('No'))
            )
        );

        if (!$model->getId()) {
            //set the default value for is_rss feed to yes for new promotion
            $model->setIsRss(1);
        }

        $form->setValues($model->getData());

        $autoGenerationCheckbox->setValue(1);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        //$form->setUseContainer(true);

        $this->setForm($form);

        // field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                $couponTypeFiled->getHtmlId(),
                $couponTypeFiled->getName()
            )->addFieldMap(
                $couponCodeFiled->getHtmlId(),
                $couponCodeFiled->getName()
            )->addFieldMap(
                $autoGenerationCheckbox->getHtmlId(),
                $autoGenerationCheckbox->getName()
            )->addFieldMap(
                $usesPerCouponFiled->getHtmlId(),
                $usesPerCouponFiled->getName()
            )->addFieldDependence(
                $couponCodeFiled->getName(),
                $couponTypeFiled->getName(),
                \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC
            )->addFieldDependence(
                $autoGenerationCheckbox->getName(),
                $couponTypeFiled->getName(),
                \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC
            )->addFieldDependence(
                $usesPerCouponFiled->getName(),
                $couponTypeFiled->getName(),
                \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC
            )
        );

        $this->_eventManager->dispatch('adminhtml_promo_quote_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
