<?php
/**
 * Google Experiment Abstract Save observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer;

abstract class AbstractSave
{
    /**
     * @var \Magento\GoogleOptimizer\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\GoogleOptimizer\Model\Code
     */
    protected $_modelCode;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * @var array
     */
    protected $_params;

    /**
     * @param \Magento\GoogleOptimizer\Helper\Data $helper
     * @param \Magento\GoogleOptimizer\Model\Code $modelCode
     * @param \Magento\Core\Controller\Request\Http $request
     */
    public function __construct(
        \Magento\GoogleOptimizer\Helper\Data $helper,
        \Magento\GoogleOptimizer\Model\Code $modelCode,
        \Magento\Core\Controller\Request\Http $request
    ) {
        $this->_helper = $helper;
        $this->_modelCode = $modelCode;
        $this->_request = $request;
    }

    /**
     * Save script after saving entity
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GoogleOptimizer\Model\Observer\Category\Save
     * @throws \InvalidArgumentException
     */
    public function saveGoogleExperimentScript($observer)
    {
        $this->_initEntity($observer);

        if ($this->_isGoogleExperimentActive()) {
            $this->_processCode();
        }

        return $this;
    }

    /**
     * Init entity
     *
     * @param \Magento\Event\Observer $observer
     */
    abstract protected function _initEntity($observer);

    /**
     * Check is Google Experiment enabled
     */
    protected function _isGoogleExperimentActive()
    {
        return $this->_helper->isGoogleExperimentActive();
    }

    /**
     * Processes Save event of the entity
     */
    protected function _processCode()
    {
        $this->_initRequestParams();

        if ($this->_isNewCode()) {
            $this->_saveCode();
        } else {
            $this->_loadCode();
            if ($this->_isEmptyCode()) {
                $this->_deleteCode();
            } else {
                $this->_saveCode();
            }
        }
    }

    /**
     * Init request params
     *
     * @throws \InvalidArgumentException
     */
    protected function _initRequestParams()
    {
        $params = $this->_request->getParam('google_experiment');
        if (!is_array($params) || !isset($params['experiment_script']) || !isset($params['code_id'])) {
            throw new \InvalidArgumentException('Wrong request parameters');
        }
        $this->_params = $params;
    }

    /**
     * Check is new model
     *
     * @return bool
     */
    protected function _isNewCode()
    {
        return empty($this->_params['code_id']);
    }

    /**
     * Save code model
     */
    protected function _saveCode()
    {
        $this->_modelCode->addData($this->_getCodeData());
        $this->_modelCode->save();
    }

    /**
     * Get data for saving code model
     *
     * @return array
     */
    abstract protected function _getCodeData();

    /**
     * Load model code
     *
     * @throws \InvalidArgumentException
     */
    protected function _loadCode()
    {
        $this->_modelCode->load($this->_params['code_id']);
        if (!$this->_modelCode->getId()) {
            throw new \InvalidArgumentException('Code does not exist');
        }
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
     * Delete model code
     *
     * @throws \InvalidArgumentException
     */
    protected function _deleteCode()
    {
        $this->_modelCode->delete();
    }
}
