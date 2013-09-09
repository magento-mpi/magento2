<?php
/**
 * Grid row url generator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_CustomerSegment_Model_Segment_Report_Detail_Grid_UrlGenerator
    extends Magento_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @param Magento_Core_Model_Registry $registry
     */
    public function __construct(Magento_Core_Model_Registry $registry)
    {
        $this->_registryManager = $registry;
        parent::__construct();
    }

    /**
     * Convert template params array and merge with preselected params
     *
     * @param Magento_Object $item
     * @return array|mixed
     */
    protected function _prepareParameters($item)
    {
        $params = array();
        foreach ($this->_extraParamsTemplate as $paramKey => $paramValueMethod) {
            $params[$paramKey] = $this->_registryManager->registry('current_customer_segment')->$paramValueMethod();
        }
        return array_merge($this->_params, $params);
    }
}
