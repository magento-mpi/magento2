<?php
/**
 * Google Experiment Abstract Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
abstract class Mage_GoogleOptimizer_Model_Observer_SaveAbstract
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
     * Init request params
     *
     * @throws InvalidArgumentException
     */
    protected function _initRequestParams()
    {
        $values = $this->_request->getParam('google_experiment');
        if (!is_array($values) || !isset($values['experiment_script']) || !isset($values['code_id'])) {
            throw new InvalidArgumentException('Wrong request parameters');
        }
        $this->_params = $values;
    }

    /**
     * Check is new model
     *
     * @return bool
     */
    protected function _isNewModelCode()
    {
        return empty($this->_params['code_id']);
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

    /**
     * Save code model
     */
    protected abstract function _saveCodeModel();

    /**
     * Load model code
     *
     * @throws InvalidArgumentException
     */
    protected function _loadCodeModel()
    {
        $this->_modelCode->load($this->_params['code_id']);
        if (!$this->_modelCode->getId()) {
            throw new InvalidArgumentException('Code does not exist');
        }
    }

    /**
     * Processes Save event of the entity
     */
    protected function _processSaveEvent()
    {
        $this->_initRequestParams();

        if ($this->_isNewModelCode()) {
            $this->_saveCodeModel();
        } else {
            $this->_loadCodeModel();
            if ($this->_isEmptyCode()) {
                $this->_modelCode->delete();
            } else {
                $this->_saveCodeModel();
            }
        }
    }
}
