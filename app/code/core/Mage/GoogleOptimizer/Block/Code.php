<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleOptmizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 *
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
     * Enter description here...
     *
     * @param unknown_type $model
     * @return unknown
     */
    protected function _setGoogleOptimizerModel($model)
    {
        $this->_googleOptmizerModel = $model;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return unknown
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
     * Enter description here...
     *
     * @return unknown
     */
    public function getScriptCode()
    {
        if (!Mage::helper('googleoptimizer')->isOptimizerActive()) {
            return '';
        }
        if (is_null($this->_scriptType)) {
            return '';
        }
        if (!($this->_getGoogleOptimizerModel() instanceof Varien_Object)) {
            return '';
        }
        return $this->_getGoogleOptimizerModel()->getData($this->_scriptType);
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $scriptType
     * @return unknown
     */
    public function setScriptType($scriptType)
    {
        if (in_array($scriptType, $this->_avaibleScriptTypes)) {
            $this->_scriptType = $scriptType;
        }
        return $this;
    }
}