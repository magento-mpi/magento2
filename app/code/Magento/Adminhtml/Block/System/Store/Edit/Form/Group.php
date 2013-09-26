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
namespace Magento\Adminhtml\Block\System\Store\Edit\Form;

class Group
    extends \Magento\Adminhtml\Block\System\Store\Edit\AbstractForm
{
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
            $websites = \Mage::getModel('Magento\Core\Model\Website')->getCollection()->toOptionArray();
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

        $categories = \Mage::getModel('Magento\Catalog\Model\Config\Source\Category')->toOptionArray();

        $fieldset->addField('group_root_category_id', 'select', array(
            'name'      => 'group[root_category_id]',
            'label'     => __('Root Category'),
            'value'     => $groupModel->getRootCategoryId(),
            'values'    => $categories,
            'required'  => true,
            'disabled'  => $groupModel->isReadOnly(),
        ));

        if ($this->_coreRegistry->registry('store_action') == 'edit') {
            $stores = \Mage::getModel('Magento\Core\Model\Store')->getCollection()
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
