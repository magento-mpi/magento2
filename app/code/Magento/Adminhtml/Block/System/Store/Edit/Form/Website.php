<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml store edit form for website
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Adminhtml_Block_System_Store_Edit_Form_Website
    extends Magento_Adminhtml_Block_System_Store_Edit_FormAbstract
{
    /**
     * @var Magento_Core_Model_Store_GroupFactory
     */
    protected $_groupFactory;

    /**
     * @param Magento_Core_Model_Store_GroupFactory $groupFactory
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_GroupFactory $groupFactory,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_groupFactory = $groupFactory;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare website specific fieldset
     *
     * @param Magento_Data_Form $form
     */
    protected function _prepareStoreFieldset(Magento_Data_Form $form)
    {
        $websiteModel = $this->_coreRegistry->registry('store_data');
        $postData = $this->_coreRegistry->registry('store_post_data');
        if ($postData) {
            $websiteModel->setData($postData['website']);
        }
        $fieldset = $form->addFieldset('website_fieldset', array(
            'legend' => __('Web Site Information')
        ));
        /* @var $fieldset Magento_Data_Form */

        $fieldset->addField('website_name', 'text', array(
            'name'      => 'website[name]',
            'label'     => __('Name'),
            'value'     => $websiteModel->getName(),
            'required'  => true,
            'disabled'  => $websiteModel->isReadOnly(),
        ));

        $fieldset->addField('website_code', 'text', array(
            'name'      => 'website[code]',
            'label'     => __('Code'),
            'value'     => $websiteModel->getCode(),
            'required'  => true,
            'disabled'  => $websiteModel->isReadOnly(),
        ));

        $fieldset->addField('website_sort_order', 'text', array(
            'name'      => 'website[sort_order]',
            'label'     => __('Sort Order'),
            'value'     => $websiteModel->getSortOrder(),
            'required'  => false,
            'disabled'  => $websiteModel->isReadOnly(),
        ));

        if ($this->_coreRegistry->registry('store_action') == 'edit') {
            $groups = $this->_groupFactory->create()->getCollection()
                ->addWebsiteFilter($websiteModel->getId())
                ->setWithoutStoreViewFilter()
                ->toOptionArray();

            $fieldset->addField('website_default_group_id', 'select', array(
                'name'      => 'website[default_group_id]',
                'label'     => __('Default Store'),
                'value'     => $websiteModel->getDefaultGroupId(),
                'values'    => $groups,
                'required'  => false,
                'disabled'  => $websiteModel->isReadOnly(),
            ));
        }

        if (!$websiteModel->getIsDefault() && $websiteModel->getStoresCount()) {
            $fieldset->addField('is_default', 'checkbox', array(
                'name'      => 'website[is_default]',
                'label'     => __('Set as Default'),
                'value'     => 1,
                'disabled'  => $websiteModel->isReadOnly(),
            ));
        } else {
            $fieldset->addField('is_default', 'hidden', array(
                'name'      => 'website[is_default]',
                'value'     => $websiteModel->getIsDefault()
            ));
        }

        $fieldset->addField('website_website_id', 'hidden', array(
            'name'  => 'website[website_id]',
            'value' => $websiteModel->getId()
        ));
    }
}
