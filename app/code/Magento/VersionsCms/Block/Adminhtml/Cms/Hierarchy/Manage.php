<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy;

/**
 * Cms Hierarchy Copy Form Container Block
 */
class Manage extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve Delete Hierarchies Url
     *
     * @return string
     */
    public function getDeleteHierarchiesUrl()
    {
        return $this->getUrl('adminhtml/*/delete');
    }

    /**
     * Retrieve Copy Hierarchy Url
     *
     * @return string
     */
    public function getCopyHierarchyUrl()
    {
        return $this->getUrl('adminhtml/*/copy');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array('data' => array('id' => 'manage_form', 'method' => 'post')));

        $currentWebsite = $this->getRequest()->getParam('website');
        $currentStore = $this->getRequest()->getParam('store');
        $excludeScopes = array();
        if ($currentStore) {
            $storeId = $this->_storeManager->getStore($currentStore)->getId();
            $excludeScopes = array(\Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_STORE . $storeId);
        } elseif ($currentWebsite) {
            $websiteId = $this->_storeManager->getWebsite($currentWebsite)->getId();
            $excludeScopes = array(\Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_WEBSITE . $websiteId);
        }
        $allStoreViews = $currentStore || $currentWebsite;
        $form->addField(
            'scopes',
            'multiselect',
            array(
                'name' => 'scopes[]',
                'class' => 'manage-select',
                'title' => __('Manage Hierarchies'),
                'values' => $this->_prepareOptions($allStoreViews, $excludeScopes)
            )
        );

        if ($currentWebsite) {
            $form->addField('website', 'hidden', array('name' => 'website', 'value' => $currentWebsite));
        }
        if ($currentStore) {
            $form->addField('store', 'hidden', array('name' => 'store', 'value' => $currentStore));
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare options for Manage select
     *
     * @param bool $all
     * @param string $excludeScopes
     * @return array
     */
    protected function _prepareOptions($all, $excludeScopes)
    {
        $storeStructure = $this->_systemStore->getStoresStructure($all);
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $options = array();

        foreach ($storeStructure as $website) {
            $value = \Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_WEBSITE . $website['value'];
            if (isset($website['children'])) {
                $website['value'] = in_array($value, $excludeScopes) ? array() : $value;
                $options[] = array(
                    'label' => $website['label'],
                    'value' => $website['value'],
                    'style' => 'border-bottom: none; font-weight: bold;'
                );
                foreach ($website['children'] as $store) {
                    if (isset($store['children']) && !in_array($store['value'], $excludeScopes)) {
                        $storeViewOptions = array();
                        foreach ($store['children'] as $storeView) {
                            $storeView['value'] = \Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_STORE .
                                $storeView['value'];
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
            } elseif ($website['value'] == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                $website['value'] = \Magento\VersionsCms\Helper\Hierarchy::SCOPE_PREFIX_STORE .
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID;
                $options[] = array('label' => $website['label'], 'value' => $website['value']);
            }
        }
        return $options;
    }
}
