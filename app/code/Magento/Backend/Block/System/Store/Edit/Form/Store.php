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
 * Adminhtml store edit form for store
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Store\Edit\Form;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Store
    extends \Magento\Backend\Block\System\Store\Edit\AbstractForm
{
    /**
     * @var \Magento\Core\Model\Website\Factory
     */
    protected $_websiteFactory;

    /**
     * @var \Magento\Core\Model\Store\Group\Factory
     */
    protected $_groupFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Core\Model\Store\Group\Factory $groupFactory
     * @param \Magento\Core\Model\Website\Factory $websiteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Core\Model\Store\Group\Factory $groupFactory,
        \Magento\Core\Model\Website\Factory $websiteFactory,
        array $data = array()
    ) {
        $this->_groupFactory = $groupFactory;
        $this->_websiteFactory = $websiteFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare store specific fieldset
     *
     * @param \Magento\Data\Form $form
     */
    protected function _prepareStoreFieldset(\Magento\Data\Form $form)
    {
        $storeModel = $this->_coreRegistry->registry('store_data');
        $postData = $this->_coreRegistry->registry('store_post_data');
        if ($postData) {
            $storeModel->setData($postData['store']);
        }
        $fieldset = $form->addFieldset('store_fieldset', array(
            'legend' => __('Store View Information')
        ));

        $storeAction = $this->_coreRegistry->registry('store_action');
        if ($storeAction == 'edit' || $storeAction == 'add' ) {
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
        $websites = $this->_websiteFactory->create()->getCollection();
        $allgroups = $this->_groupFactory->create()->getCollection();
        $groups = array();
        foreach ($websites as $website) {
            $values = array();
            foreach ($allgroups as $group) {
                if ($group->getWebsiteId() == $website->getId()) {
                    $values[] = array('label' => $group->getName(), 'value' => $group->getId());
                }
            }
            $groups[] = array('label' => $website->getName(), 'value' => $values);
        }
        return $groups;
    }
}
