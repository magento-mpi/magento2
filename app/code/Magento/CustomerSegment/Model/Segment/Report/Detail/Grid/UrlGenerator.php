<?php
/**
 * Grid row url generator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Report\Detail\Grid;

class UrlGenerator
    extends \Magento\Backend\Model\Widget\Grid\Row\UrlGenerator
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Core\Model\Registry $registry
     */
    public function __construct(\Magento\Backend\Model\Url $backendUrl, \Magento\Core\Model\Registry $registry)
    {
        $this->_registryManager = $registry;
        parent::__construct($backendUrl);
    }

    /**
     * Convert template params array and merge with preselected params
     *
     * @param \Magento\Object $item
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
