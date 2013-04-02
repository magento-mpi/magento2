<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration object for Translate
 */
class Mage_Core_Model_Translate_Config
{
    /**
     * Constant for parameters array for this config instance.
     */
    const CONFIG_PARAMS = 'params';

    /** @var Mage_Core_Model_App_Area */
    protected $_area;

    /**
     * @var mixed|Mage_Core_Model_Translate_TranslateInterface
     */
    protected $_inlineType;

    /**
     * @var array
     */
    protected $_params = array();

    /**
     * @var bool
     */
    protected $_forceReload = false;

    /**
     * This helper method returns the area for the current translation.
     *
     * @return Mage_Core_Model_App_Area
     */
    public function getArea()
    {
        if (null === $this->_area) {
            $this->_area = Mage::getObjectManager()->get('Mage_Core_Model_App_Area');
        }
        return $this->_area;
    }

    /**
     * This method sets the area for the current translation.
     *
     * @param Mage_Core_Model_App_Area $area
     */
    public function setArea($area)
    {
        $this->_area = $area;
    }

    /**
     * This method returns the inline type for the current translation.
     *
     * @return mixed|Mage_Core_Model_Translate_TranslateInterface
     */
    public function getInlineType()
    {
        return $this->_inlineType;
    }

    /**
     * This method sets the inline type for the current translation.
     *
     * @param $inlineType mixed|Mage_Core_Model_Translate_TranslateInterface
     * @return Mage_Core_Model_Translate_Config
     */
    public function setInlineType($inlineType)
    {
        $this->_inlineType = $inlineType;
        return $this;
    }

    /**
     * This method adds a parameter to this config instance.
     *
     * @param $name string
     * @param $value mixed
     */
    public function addParam($name, $value)
    {
        $this->_params[$name] = $value;
    }

    /**
     * This method returns the array of parameters specific to this config instance.
     *
     * @return array
     */
    public function getParams()
    {
        return array(self::CONFIG_PARAMS => $this->_params);
    }

    /**
     * This method returns the force reload indicator of this config instance.
     *
     * @return bool
     */
    public function getForceReload()
    {
        return $this->_forceReload;
    }

    /**
     * This method sets the force reload indicator of this config instance.
     *
     * @param $forceReload
     */
    public function setForceReload($forceReload)
    {
        $this->_forceReload = $forceReload;
    }
}
