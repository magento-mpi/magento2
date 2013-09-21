<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms Hierarchy Copy Form Container Block
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Manage extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_System_Store $systemStore
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_System_Store $systemStore,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_systemStore = $systemStore;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Retrieve Delete Hierarchies Url
     *
     * @return string
     */
    public function getDeleteHierarchiesUrl()
    {
        return $this->getUrl('*/*/delete');
    }

    /**
     * Retrieve Copy Hierarchy Url
     *
     * @return string
     */
    public function getCopyHierarchyUrl()
    {
        return $this->getUrl('*/*/copy');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Edit_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'manage_form',
                'method'    => 'post',
            ))
        );

        $currentWebsite = $this->getRequest()->getParam('website');
        $currentStore   = $this->getRequest()->getParam('store');
        $excludeScopes = array();
        if ($currentStore) {
            $storeId = $this->_storeManager->getStore($currentStore)->getId();
            $excludeScopes = array(
                Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_STORE . $storeId
            );
        } elseif ($currentWebsite) {
            $websiteId = $this->_storeManager->getWebsite($currentWebsite)->getId();
            $excludeScopes = array(
                Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_WEBSITE . $websiteId
            );
        }
        $allStoreViews = $currentStore || $currentWebsite;
        $form->addField('scopes', 'multiselect', array(
            'name'      => 'scopes[]',
            'class'     => 'manage-select',
            'title'     => __('Manage Hierarchies'),
            'values'    => $this->_prepareOptions($allStoreViews, $excludeScopes)
        ));

        if ($currentWebsite) {
            $form->addField('website', 'hidden', array(
                'name'   => 'website',
                'value' => $currentWebsite,
            ));
        }
        if ($currentStore) {
            $form->addField('store', 'hidden', array(
                'name'   => 'store',
                'value' => $currentStore,
            ));
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare options for Manage select
     *
     * @param boolean $all
     * @param string $excludeScopes
     * @return array
     */
    protected function _prepareOptions($all = false, $excludeScopes)
    {
        $storeStructure = $this->_systemStore->getStoresStructure($all);
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $options = array();

        foreach ($storeStructure as $website) {
            $value = Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_WEBSITE . $website['value'];
            if (isset($website['children'])) {
                $website['value'] = in_array($value, $excludeScopes) ? array() : $value;
                $options[] = array(
                    'label' => $website['label'],
                    'value' => $website['value'],
                    'style' => 'border-bottom: none; font-weight: bold;',
                );
                foreach ($website['children'] as $store) {
                    if (isset($store['children']) && !in_array($store['value'], $excludeScopes)) {
                        $storeViewOptions = array();
                        foreach ($store['children'] as $storeView) {
                            $storeView['value'] = Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_STORE
                                . $storeView['value'];
                            if (!in_array($storeView['value'], $excludeScopes)) {
                                $storeView['label'] = str_repeat($nonEscapableNbspChar, 4) . $storeView['label'];
                                $storeViewOptions[] = $storeView;
                            }
                        }
                        if ($storeViewOptions) {
                            $options[] = array(
                                'label' => str_repeat($nonEscapableNbspChar, 4) . $store['label'],
                                'value' => $storeViewOptions
                            );
                        }
                    }
                }
            } elseif ($website['value'] == Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
                $website['value'] = Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_STORE
                                    . Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID;
                $options[] = array(
                    'label' => $website['label'],
                    'value' => $website['value'],
                );
            }
        }
        return $options;
    }
}
