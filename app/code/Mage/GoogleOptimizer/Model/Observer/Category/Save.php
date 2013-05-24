<?php
/**
 * Google Experiment Category Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Category_Save
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
     * @var Mage_Catalog_Model_Category
     */
    protected $_category;

    /**
     * @var array
     */
    protected $_params;

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
     * @return Mage_GoogleOptimizer_Model_Observer_Category_Save
     * @throws InvalidArgumentException
     */
    public function saveCategoryGoogleExperimentScript($observer)
    {
        $this->_category = $observer->getEvent()->getCategory();

        if (!$this->_helper->isGoogleExperimentActive($this->_category->getStoreId())) {
            return $this;
        }

        $this->_initRequestParams();

        if ($this->_isNewModelCode()) {
            $this->_saveCode();
        } else {
            $this->_loadCode();
            if ($this->_isEmptyCode()) {
                $this->_modelCode->delete();
            } else {
                $this->_saveCode();
            }
        }

        return $this;
    }

    /**
     * Init request params
     *
     * @throws InvalidArgumentException
     */
    protected function _initRequestParams()
    {
        $values = $this->_request->getParam('google_experiment');
        if (!is_array($values) || !isset($values['experiment_script']) || !isset($values['code_id'])) {
            throw new InvalidArgumentException($this->_helper->__('Wrong arguments'));
        }
        $this->_params = $values;
    }

    /**
     * Check is new modek
     *
     * @return bool
     */
    protected function _isNewModelCode()
    {
        return empty($this->_params['code_id']);
    }

    /**
     * Load model code
     *
     * @throws InvalidArgumentException
     */
    protected function _loadCode()
    {
        $this->_modelCode->load($this->_params['code_id']);
        if (!$this->_modelCode->getId()) {
            throw new InvalidArgumentException($this->_helper->__('Wrong arguments'));
        }
    }

    /**
     * Save code model
     */
    protected function _saveCode()
    {
        $this->_modelCode->setData(array(
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY,
            'entity_id' => $this->_category->getId(),
            'store_id' => $this->_category->getStoreId(),
            'experiment_script' => $this->_params['experiment_script'],
        ));
        $this->_modelCode->save();
    }

    /**
     * Is empty code
     *
     * @return bool
     */
    protected function _isEmptyCode()
    {
        return empty($this->_params['experiment_script']);
    }
}
