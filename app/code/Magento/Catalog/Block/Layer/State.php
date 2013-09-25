<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layered navigation state
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Layer_State extends Magento_Core_Block_Template
{
    protected $_template = 'layer/state.phtml';

    /**
     * Catalog layer
     *
     * @var Magento_Catalog_Model_Layer
     */
    protected $_catalogLayer;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Layer $catalogLayer
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Layer $catalogLayer,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_catalogLayer = $catalogLayer;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = array();
        }
        return $filters;
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = array();
        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $filterState;
        $params['_escape']      = true;
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

    /**
     * Retrieve Layer object
     *
     * @return Magento_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer($this->_catalogLayer);
        }
        return $this->_getData('layer');
    }
}
