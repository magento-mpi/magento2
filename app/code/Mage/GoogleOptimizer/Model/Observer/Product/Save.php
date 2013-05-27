<?php
/**
 * Google Experiment Product Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Product_Save extends Mage_GoogleOptimizer_Model_Observer_SaveAbstract
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Save product script after saving product
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleOptimizer_Model_Observer_Product_Save
     * @throws InvalidArgumentException
     */
    public function saveProductGoogleExperimentScript($observer)
    {
        $this->_product = $observer->getEvent()->getProduct();

        if (!$this->_helper->isGoogleExperimentActive($this->_product->getStoreId())) {
            return $this;
        }

        $this->_processSaveEvent();

        return $this;
    }

    /**
     * Save code model
     */
    protected function _saveCodeModel()
    {
        $this->_modelCode->addData(array(
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            'entity_id' => $this->_product->getId(),
            'store_id' => $this->_product->getStoreId(),
            'experiment_script' => $this->_params['experiment_script'],
        ));
        $this->_modelCode->save();
    }
}
