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
 * Cms Pages Hierarchy Widget Radio Block
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Hierarchy_Widget_Radio extends Magento_Adminhtml_Block_Template
{
    /**
     * Unique Hash Id
     *
     * @var null
     */
    protected $_uniqId = null;

    /**
     * Widget Parameters
     *
     * @var array
     */
    protected $_params = array();

    /**
     * All Store Views
     *
     * @var array
     */
    protected $_allStoreViews = array();

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'hierarchy/widget/radio.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_VersionsCms_Model_Hierarchy_Node
     */
    protected $_hierarchyNode;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_VersionsCms_Model_Hierarchy_Node $hierarchyNode
     * @param Magento_Core_Model_System_Store $systemStore
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_VersionsCms_Model_Hierarchy_Node $hierarchyNode,
        Magento_Core_Model_System_Store $systemStore,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_hierarchyNode = $hierarchyNode;
        $this->_systemStore = $systemStore;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get all Store View labels and ids
     *
     * @return array
     */
    public function getAllStoreViews()
    {
        if (empty($this->_allStoreViews)) {
            $storeValues = $this->_systemStore->getStoreValuesForForm(false, true);
            foreach ($storeValues as $view) {
                if (is_array($view['value']) && empty($view['value'])) {
                    continue;
                }
                if ($view['value'] == 0) {
                    $view['value'] = array(array('label' => $view['label'],'value' => $view['value']));
                }
                foreach ($view['value'] as $store) {
                    $this->_allStoreViews[] = $store;
                }
            }
        }

        return $this->_allStoreViews;
    }

    /**
     * Get array with Store View labels and ids
     *
     * @return array
     */
    public function getAllStoreViewsList()
    {
        $allStoreViews = $this->getAllStoreViews();
        reset($allStoreViews);
        $storeViews[] = current($allStoreViews);
        unset($allStoreViews);

        $storeValues = $this->_systemStore->getStoreCollection();

        foreach ($storeValues as $store) {
            $storeViews[] = array(
                'label' => $store->getName(),
                'value' => $store->getId()
            );
        }

        return $storeViews;
    }

    /**
     * Get All Store Views Ids array
     *
     * @return array
     */
    public function getAllStoreViewIds()
    {
        $ids = array();
        foreach($this->getAllStoreViews() as $view) {
            $ids[] = $view['value'];
        }

        return $ids;
    }

    /**
     * Get Unique Hash
     *
     * @return null|string
     */
    public function getUniqHash()
    {
        if ($this->getUniqId() !== null) {
            $id = explode('_', $this->getUniqId());
            if (isset($id[1])) {
                return $id[1];
            }
        }
        return null;
    }

    /**
     * Get Widget Parameters
     *
     * @return array
     */
    public function getParameters()
    {
        if (empty($this->_params)) {
            $widget = $this->_coreRegistry->registry('current_widget_instance');
            $this->_params = $widget ? $widget->getWidgetParameters() : array();
        }
        return $this->_params;
    }

    /**
     * Get Parameter Value
     *
     * @param int $key
     * @return string
     */
    public function getParamValue($key)
    {
        $params = $this->getParameters();

        return (isset($params[$key])) ? $params[$key] : '';
    }

    /**
     * Get Label Value By Node Id
     *
     * @param int $nodeId
     * @return string
     */
    public function getLabelByNodeId($nodeId)
    {
        if ($nodeId) {
            $node = $this->_hierarchyNode->load($nodeId);
            if ($node->getId()) {
                return $node->getLabel();
            }
        }
        return '';
    }

    /**
     * Retrieve block HTML markup
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_storeManager->isSingleStoreMode() == false ? parent::_toHtml() : '';
    }
}
