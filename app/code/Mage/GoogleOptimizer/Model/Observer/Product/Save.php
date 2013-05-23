<?php
/**
 * Google Experiment Product Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Product_Save
{
    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_GoogleOptimizer_Model_Code
     */
    protected $_modelCode;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Mage_GoogleOptimizer_Helper_Data $helper
     * @param Mage_GoogleOptimizer_Model_Code $modelCode
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(
        Mage_GoogleOptimizer_Helper_Data $helper,
        Mage_GoogleOptimizer_Model_Code $modelCode,
        Mage_Core_Controller_Request_Http $request
    ) {
        $this->_helper = $helper;
        $this->_modelCode = $modelCode;
        $this->_request = $request;
    }

    /**
     * Save product scripts after saving product
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleOptimizer_Model_Observer_Product_Save
     */
    public function saveProductGoogleExperimentScript($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        if (!$this->_helper->isGoogleExperimentActive($product->getStoreId())) {
            return $this;
        }

        $values = $this->_request->getParam('google_experiment');
        if (!empty($values['code_id'])) {
            $this->_modelCode->load($values['code_id']);
        }

        if ($values['experiment_script']) {
            $data = array(
                'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
                'entity_id' => $product->getId(),
                'store_id' => $product->getStoreId(),
                'experiment_script' => $values['experiment_script'],
            );

            $this->_modelCode->addData($data);
            $this->_modelCode->save();
        }

        if ($this->_modelCode->getId() && !$values['experiment_script']) {
            $this->_modelCode->delete();
        }

        return $this;
    }
}
