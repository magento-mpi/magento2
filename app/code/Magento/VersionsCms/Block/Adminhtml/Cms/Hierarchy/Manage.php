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
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Manage extends Magento_Adminhtml_Block_Widget_Form
{

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
        $form = new \Magento\Data\Form(array(
            'id'        => 'manage_form',
            'method'    => 'post'
        ));

        $currentWebsite = $this->getRequest()->getParam('website');
        $currentStore   = $this->getRequest()->getParam('store');
        $excludeScopes = array();
        if ($currentStore) {
            $storeId = Mage::app()->getStore($currentStore)->getId();
            $excludeScopes = array(
                Magento_VersionsCms_Helper_Hierarchy::SCOPE_PREFIX_STORE . $storeId
            );
        } elseif ($currentWebsite) {
            $websiteId = Mage::app()->getWebsite($currentWebsite)->getId();
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
        $storeStructure = Mage::getSingleton('Magento_Core_Model_System_Store')
                ->getStoresStructure($all);
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
