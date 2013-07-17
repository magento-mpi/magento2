<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme selectors tabs container
 *
 * @method int getThemeId()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_StoreView extends Mage_Backend_Block_Template
{
    /**
     * Website collection
     *
     * @var Mage_Core_Model_Resource_Website_Collection
     */
    protected $_websiteCollection;

    /**
     * @var Mage_Theme_Model_Config_Customization
     */
    protected $_customizationConfig;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_Resource_Website_Collection $websiteCollection
     * @param Mage_Theme_Model_Config $themeConfig
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_Resource_Website_Collection $websiteCollection,
        Mage_Theme_Model_Config_Customization $customizationConfig,
        array $data = array()
    ) {
        $this->_websiteCollection = $websiteCollection;
        $this->_customizationConfig = $customizationConfig;

        parent::__construct($context, $data);
    }

    /**
     * Get website collection with stores and store-views joined
     *
     * @return Mage_Core_Model_Resource_Website_Collection
     */
    public function getCollection()
    {
        return $this->_websiteCollection->joinGroupAndStore();
    }

    /**
     * Get website, stores and store-views
     *
     * @return Mage_Core_Model_Resource_Website_Collection
     */
    public function getWebsiteStructure()
    {
        $structure = array();
        $website = null;
        $store = null;
        $storeView = null;
        /** @var $row Mage_Core_Model_Website */
        foreach ($this->getCollection() as $row) {
            $website = $row->getName();
            $store = $row->getGroupTitle();
            $storeView = $row->getStoreTitle();
            if (!isset($structure[$website])) {
                $structure[$website] = array();
            }
            if (!isset($structure[$website][$store])) {
                $structure[$website][$store] = array();
            }
            $structure[$website][$store][$storeView] = (int)$row->getStoreId();
        }

        return $structure;
    }

    /**
     * Get assign to multiple storeview button
     *
     * @return string
     */
    public function getAssignNextButtonHtml()
    {
        /** @var $assignSaveButton Mage_Backend_Block_Widget_Button */
        $assignSaveButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignSaveButton->setData(array(
            'label'     => $this->__('Assign'),
            'class'     => 'action-save primary',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'assign-confirm',
                        'target' => 'body',
                        'eventData' => array()
                    ),
                ),
            )
        ));

        return $assignSaveButton->toHtml();
    }

    /**
     * Get an array of stores grouped by theme customization it uses.
     *
     * The structure is the following:
     *   array(
     *      theme_id => array(store_id)
     *   )
     *
     * @return array
     */
    protected function _getStoresByThemes()
    {
        $assignedThemeIds = array_map(
            function ($theme) {
                return $theme->getId();
            },
            $this->_customizationConfig->getAssignedThemeCustomizations()
        );

        $storesByThemes = array();
        foreach ($this->_customizationConfig->getStoresByThemes() as $themeId => $stores) {
            /* NOTE
                We filter out themes not included to $assignedThemeIds array so we only get actually "assigned"
                themes. So if theme is assigned to store or website and used by store-view only via config fall-back
                mechanism it will not get to the resulting $storesByThemes array.
            */
            if (!in_array($themeId, $assignedThemeIds)) {
                continue;
            }

            $storesByThemes[$themeId] = array();
            /** @var $store Mage_Core_Model_Store */
            foreach ($stores as $store) {
                $storesByThemes[$themeId][] = (int)$store->getId();
            }
        }

        return $storesByThemes;
    }

    /**
     * Get the flag if there are multiple store-views in Magento
     *
     * @return bool
     */
    protected function _hasMultipleStores()
    {
        $isMultipleMode = false;
        $tmpStore = null;
        foreach ($this->_customizationConfig->getStoresByThemes() as $stores) {
            /** @var $store Mage_Core_Model_Store */
            foreach ($stores as $store) {
                if ($tmpStore === null) {
                    $tmpStore = $store->getId();
                } elseif ($tmpStore != $store->getId()) {
                    $isMultipleMode = true;
                    break(2);
                }
            }
        }

        return $isMultipleMode;
    }

    /**
     * Get options for JS widget vde.storeSelector
     *
     * @return string
     */
    public function getOptionsJson()
    {
        $options = array();
        $options['storesByThemes']    = $this->_getStoresByThemes();
        $options['assignUrl']         = $this->getUrl('*/*/assignThemeToStore', array(
            'theme_id' => $this->getThemeId()
        ));
        $options['afterAssignUrl']    = $this->getUrl('*/*/index');
        $options['hasMultipleStores'] = $this->_hasMultipleStores();

        $options['actionOnAssign']   = $this->getData('actionOnAssign');
        $options['afterAssignOpen']  = false;

        /** @var $helper Mage_Core_Helper_Data */
        $helper = $this->helper('Mage_Core_Helper_Data');

        return $helper->jsonEncode($options);
    }
}
