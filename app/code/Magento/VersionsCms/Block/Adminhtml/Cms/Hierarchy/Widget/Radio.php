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
 *
 * @category   Magento
 * @package    Magento_VersionsCms
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
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get all Store View labels and ids
     *
     * @return array
     */
    public function getAllStoreViews()
    {
        if (empty($this->_allStoreViews)) {
            $storeValues = Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm(false, true);
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

        $storeValues = Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreCollection();

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
            $node = Mage::getSingleton('Magento_VersionsCms_Model_Hierarchy_Node')->load($nodeId);
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
        return Mage::app()->isSingleStoreMode() == false ? parent::_toHtml() : '';
    }
}
