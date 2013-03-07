<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Optimizer Scripts Block
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Code extends Mage_Core_Block_Template
{
    protected $_scriptType          = null;
    protected $_googleOptmizerModel = null;
    protected $_avaibleScriptTypes = array('control_script', 'tracking_script', 'conversion_script');

    /**
     * override this method if need something special for type of script
     *
     * @return Mage_GoogleOptimizer_Block_Code
     */
    protected function _initGoogleOptimizerModel()
    {
        return $this;
    }

    /**
     * Setting google optimizer model
     *
     * @param Varien_Object $model
     * @return Mage_GoogleOptimizer_Block_Code
     */
    protected function _setGoogleOptimizerModel($model)
    {
        $this->_googleOptmizerModel = $model;
        return $this;
    }

    /**
     * Return google optimizer model
     *
     * @return Varien_Object
     */
    protected function _getGoogleOptimizerModel()
    {
        return $this->_googleOptmizerModel;
    }

    protected function _toHtml()
    {
        return parent::_toHtml() . $this->getScriptCode();
    }

    /**
     * Return script by type $this->_scriptType
     *
     * @return string
     */
    public function getScriptCode()
    {
        if (!Mage::helper('Mage_GoogleOptimizer_Helper_Data')->isOptimizerActive()) {
            return '';
        }
        if (is_null($this->_scriptType)) {
            return '';
        }
        $this->_initGoogleOptimizerModel();
        if (!($this->_getGoogleOptimizerModel() instanceof Varien_Object)) {
            return '';
        }
        return $this->_getGoogleOptimizerModel()->getData($this->_scriptType);
    }

    /**
     * Check than set script type
     *
     * @param string $scriptType
     * @return Mage_GoogleOptimizer_Block_Code
     */
    public function setScriptType($scriptType)
    {
        if (in_array($scriptType, $this->_avaibleScriptTypes)) {
            $this->_scriptType = $scriptType;
        }
        return $this;
    }
}
