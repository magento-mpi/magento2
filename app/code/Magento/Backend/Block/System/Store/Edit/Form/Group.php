<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml store edit form for group
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Store\Edit\Form;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Group
    extends \Magento\Backend\Block\System\Store\Edit\AbstractForm
{
    /**
     * @var \Magento\Catalog\Model\Config\Source\Category
     */
    protected $_category;

    /**
     * @var \Magento\Core\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Core\Model\Website\Factory
     */
    protected $_websiteFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Catalog\Model\Config\Source\Category $category
     * @param \Magento\Core\Model\StoreFactory $storeFactory
     * @param \Magento\Core\Model\Website\Factory $websiteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Catalog\Model\Config\Source\Category $category,
        \Magento\Core\Model\StoreFactory $storeFactory,
        \Magento\Core\Model\Website\Factory $websiteFactory,
        array $data = array()
    ) {
        $this->_category = $category;
        $this->_storeFactory = $storeFactory;
        $this->_websiteFactory = $websiteFactory;
        parent::__construct($context, $coreData, $registry, $formFactory, $data);
    }

    /**
     * Prepare group specific fieldset
     *
     * @param \Magento\Data\Form $form
     */
    protected function _prepareStoreFieldset(\Magento\Data\Form $form)
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
