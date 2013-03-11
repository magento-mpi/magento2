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

    /**
     * Constant for area parameter for this config instance.
     */
    const PARAM_AREA = 'area';

    /**
     * @var mixed|Mage_Core_Model_Translate_InlineAbstract
     */
    protected $_inlineType;

    /**
     * @var array
     */
    protected $_params;

    /**
     * @var bool
     */
    protected $_forceReload;

    /**
     * Class constructor
     */
    public function __construct(
    ) {
        $this->_inlineType = null;
        $this->_params = array();
        $this->_forceReload = false;
    }

    /**
     * This method returns the inline type for the current translation.
     * @return mixed|Mage_Core_Model_Translate_InlineAbstract
     */
    public function getInlineType()
    {
        return $this->_inlineType;
    }

    /**
     * @param $inlineType mixed|Mage_Core_Model_Translate_InlineAbstract
     */
    public function setInlineType($inlineType)
    {
        $this->_inlineType = $inlineType;
    }

    /**
     * @param $name
     * @param $value
     */
    public function addParam($name, $value)
    {
        $this->_params[$name] = $value;
    }

    /**
     * This method returns the array of parameters specific to this config instance.
     * @return array
     */
    public function getParams()
    {
        return array(CONFIG_PARAMS => $this->_params);
    }

    /**
     * This helper method returns the 'area' parameter.
     * @return mixed
     */
    public function getArea()
    {
        return $this->_params[self::PARAM_AREA];
    }

    /**
     * This method returns the force reload indicator of this config instance.
     * @return bool
     */
    public function getForceReload()
    {
        return $this->_forceReload;
    }

    /**
     * @param $forceReload
     */
    public function setForceReload($forceReload)
    {
        $this->_forceReload = $forceReload;
    }
}
