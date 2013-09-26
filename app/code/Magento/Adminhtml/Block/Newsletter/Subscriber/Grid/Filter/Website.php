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
 * Adminhtml newsletter subscribers grid website filter
 */
class Magento_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Website
    extends Magento_Backend_Block_Widget_Grid_Column_Filter_Select
{
    protected $_websiteCollection = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $resourceHelper, $data);
    }

    protected function _getOptions()
    {
        $result = $this->getCollection()->toOptionArray();
        array_unshift($result, array('label'=>null, 'value'=>null));
        return $result;
    }

    public function getCollection()
    {
        if(is_null($this->_websiteCollection)) {
            $this->_websiteCollection = Mage::getResourceModel('Magento_Core_Model_Resource_Website_Collection')
                ->load();
        }

        $this->_coreRegistry->register('website_collection', $this->_websiteCollection);

        return $this->_websiteCollection;
    }

    public function getCondition()
    {
        $id = $this->getValue();
        if (!$id) {
            return null;
        }

        $website = Mage::app()->getWebsite($id);
        return array('in' => $website->getStoresIds(true));
    }
}
