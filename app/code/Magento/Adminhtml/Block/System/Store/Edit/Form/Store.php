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
 * Adminhtml store edit form for store
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_System_Store_Edit_Form_Store
    extends Magento_Adminhtml_Block_System_Store_Edit_FormAbstract
{
    /**
     * Prepare store specific fieldset
     *
     * @param \Magento\Data\Form $form
     */
    protected function _prepareStoreFieldset(\Magento\Data\Form $form)
    {
        $storeModel = Mage::registry('store_data');
        if ($postData = Mage::registry('store_post_data')) {
            $storeModel->setData($postData['store']);
        }
        $fieldset = $form->addFieldset('store_fieldset', array(
            'legend' => __('Store View Information')
        ));

        if (Mage::registry('store_action') == 'edit' || Mage::registry('store_action') == 'add' ) {
            $fieldset->addField('store_group_id', 'select', array(
                'name'      => 'store[group_id]',
                'label'     => __('Store'),
                'value'     => $storeModel->getGroupId(),
                'values'    => $this->_getStoreGroups(),
                'required'  => true,
                'disabled'  => $storeModel->isReadOnly(),
            ));
            if ($storeModel->getId() && $storeModel->getGroup()->getDefaultStoreId() == $storeModel->getId()) {
                if ($storeModel->getGroup() && $storeModel->getGroup()->getStoresCount() > 1) {
                    $form->getElement('store_group_id')->setDisabled(true);

                    $fieldset->addField('store_hidden_group_id', 'hidden', array(
                        'name'      => 'store[group_id]',
                        'no_span'   => true,
                        'value'     => $storeModel->getGroupId()
                    ));
                } else {
                    $fieldset->addField('store_original_group_id', 'hidden', array(
                        'name'      => 'store[original_group_id]',
                        'no_span'   => true,
                        'value'     => $storeModel->getGroupId()
                    ));
                }
            }
        }

        $fieldset->addField('store_name', 'text', array(
            'name'      => 'store[name]',
            'label'     => __('Name'),
            'value'     => $storeModel->getName(),
            'required'  => true,
            'disabled'  => $storeModel->isReadOnly(),
        ));
        $fieldset->addField('store_code', 'text', array(
            'name'      => 'store[code]',
            'label'     => __('Code'),
            'value'     => $storeModel->getCode(),
            'required'  => true,
            'disabled'  => $storeModel->isReadOnly(),
        ));

        $fieldset->addField('store_is_active', 'select', array(
            'name'      => 'store[is_active]',
            'label'     => __('Status'),
            'value'     => $storeModel->getIsActive(),
            'options'   => array(
                0 => __('Disabled'),
                1 => __('Enabled')),
            'required'  => true,
            'disabled'  => $storeModel->isReadOnly(),
        ));

        $fieldset->addField('store_sort_order', 'text', array(
            'name'      => 'store[sort_order]',
            'label'     => __('Sort Order'),
            'value'     => $storeModel->getSortOrder(),
            'required'  => false,
            'disabled'  => $storeModel->isReadOnly(),
        ));

        $fieldset->addField('store_is_default', 'hidden', array(
            'name'      => 'store[is_default]',
            'no_span'   => true,
            'value'     => $storeModel->getIsDefault(),
        ));

        $fieldset->addField('store_store_id', 'hidden', array(
            'name'      => 'store[store_id]',
            'no_span'   => true,
            'value'     => $storeModel->getId(),
            'disabled'  => $storeModel->isReadOnly(),
        ));
    }

    /**
     * Retrieve list of store groups
     *
     * @return array
     */
    protected function _getStoreGroups()
    {
        $websites = Mage::getModel('Magento_Core_Model_Website')->getCollection();
        $allgroups = Mage::getModel('Magento_Core_Model_Store_Group')->getCollection();
        $groups = array();
        foreach ($websites as $website) {
            $values = array();
            foreach ($allgroups as $group) {
                if ($group->getWebsiteId() == $website->getId()) {
                    $values[] = array('label'=>$group->getName(),'value'=>$group->getId());
                }
            }
            $groups[] = array('label'=>$website->getName(),'value'=>$values);
        }
        return $groups;
    }
}
