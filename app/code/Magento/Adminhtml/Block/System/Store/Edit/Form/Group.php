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
 * Adminhtml store edit form for group
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Adminhtml_Block_System_Store_Edit_Form_Group
    extends Magento_Adminhtml_Block_System_Store_Edit_FormAbstract
{
    /**
     * @var Magento_Catalog_Model_Config_Source_Category
     */
    protected $_category;

    /**
     * @var Magento_Core_Model_StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var Magento_Core_Model_Website_Factory
     */
    protected $_websiteFactory;

    /**
     * @param Magento_Catalog_Model_Config_Source_Category $category
     * @param Magento_Core_Model_StoreFactory $storeFactory
     * @param Magento_Core_Model_Website_Factory $websiteFactory
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Config_Source_Category $category,
        Magento_Core_Model_StoreFactory $storeFactory,
        Magento_Core_Model_Website_Factory $websiteFactory,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_category = $category;
        $this->_storeFactory = $storeFactory;
        $this->_websiteFactory = $websiteFactory;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare group specific fieldset
     *
     * @param Magento_Data_Form $form
     */
    protected function _prepareStoreFieldset(Magento_Data_Form $form)
    {
        $groupModel = $this->_coreRegistry->registry('store_data');
        $postData = $this->_coreRegistry->registry('store_post_data');
        if ($postData) {
            $groupModel->setData($postData['group']);
        }

        $fieldset = $form->addFieldset('group_fieldset', array(
            'legend' => __('Store Information')
        ));

        $storeAction = $this->_coreRegistry->registry('store_action');
        if ($storeAction == 'edit' || $storeAction == 'add') {
            $websites = $this->_websiteFactory->create()->getCollection()->toOptionArray();
            $fieldset->addField('group_website_id', 'select', array(
                'name'      => 'group[website_id]',
                'label'     => __('Web Site'),
                'value'     => $groupModel->getWebsiteId(),
                'values'    => $websites,
                'required'  => true,
                'disabled'  => $groupModel->isReadOnly(),
            ));

            if ($groupModel->getId() && $groupModel->getWebsite()->getDefaultGroupId() == $groupModel->getId()) {
                if ($groupModel->getWebsite()->getIsDefault() || $groupModel->getWebsite()->getGroupsCount() == 1) {
                    $form->getElement('group_website_id')->setDisabled(true);

                    $fieldset->addField('group_hidden_website_id', 'hidden', array(
                        'name'      => 'group[website_id]',
                        'no_span'   => true,
                        'value'     => $groupModel->getWebsiteId()
                    ));
                } else {
                    $fieldset->addField('group_original_website_id', 'hidden', array(
                        'name'      => 'group[original_website_id]',
                        'no_span'   => true,
                        'value'     => $groupModel->getWebsiteId()
                    ));
                }
            }
        }

        $fieldset->addField('group_name', 'text', array(
            'name'      => 'group[name]',
            'label'     => __('Name'),
            'value'     => $groupModel->getName(),
            'required'  => true,
            'disabled'  => $groupModel->isReadOnly(),
        ));

        $categories = $this->_category->toOptionArray();

        $fieldset->addField('group_root_category_id', 'select', array(
            'name'      => 'group[root_category_id]',
            'label'     => __('Root Category'),
            'value'     => $groupModel->getRootCategoryId(),
            'values'    => $categories,
            'required'  => true,
            'disabled'  => $groupModel->isReadOnly(),
        ));

        if ($this->_coreRegistry->registry('store_action') == 'edit') {
            $stores = $this->_storeFactory->create()->getCollection()
                ->addGroupFilter($groupModel->getId())->toOptionArray();
            $fieldset->addField('group_default_store_id', 'select', array(
                'name'      => 'group[default_store_id]',
                'label'     => __('Default Store View'),
                'value'     => $groupModel->getDefaultStoreId(),
                'values'    => $stores,
                'required'  => false,
                'disabled'  => $groupModel->isReadOnly(),
            ));
        }

        $fieldset->addField('group_group_id', 'hidden', array(
            'name'      => 'group[group_id]',
            'no_span'   => true,
            'value'     => $groupModel->getId()
        ));
    }
}
