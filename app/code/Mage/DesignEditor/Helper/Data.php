<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Editor main helper
 */
class Mage_DesignEditor_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**#@+
     * XML paths to VDE settings
     */
    const XML_PATH_FRONT_NAME           = 'vde/design_editor/frontName';
    const XML_PATH_DISABLED_CACHE_TYPES = 'vde/design_editor/disabledCacheTypes';
    /**#@-*/

    /**
     * Parameter to indicate the translation mode (null, text, script, or alt).
     */
    const TRANSLATION_MODE = "translation_mode";

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_configuration;

    /**
     * @var bool
     */
    protected $_isVdeRequest;

    /**
     * @var mixed
     */
    protected $_translationMode;

    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config $configuration
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config $configuration
    ) {
        parent::__construct($context);
        $this->_configuration = $configuration;
    }

    /**
     * Get VDE front name prefix
     *
     * @return string
     */
    public function getFrontName()
    {
        return (string)$this->_configuration->getNode(self::XML_PATH_FRONT_NAME);
    }

    /**
     * Get disabled cache types in VDE mode
     *
     * @return array
     */
    public function getDisabledCacheTypes()
    {
        $cacheTypes = $this->_configuration->getNode(self::XML_PATH_DISABLED_CACHE_TYPES)->asArray();
        return array_keys($cacheTypes);
    }

    /**
     * Returns the translate object for this helper.
     *
     * @return Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * This method returns an indicator of whether or not the current request is for vde.
     *
     * @param $request Mage_Core_Controller_Request_Http
     * @return _isVdeRequest bool
     */
    public function isVdeRequest(Mage_Core_Controller_Request_Http $request = null)
    {
        if (null !== $request) {
            list($frontName, $currentMode, $themeId) = explode('/', trim($request->getOriginalPathInfo(), '/'));
            $vdeFrontName = $this->getFrontName();
            $this->_isVdeRequest = $frontName === $vdeFrontName
                && in_array($currentMode, $this->getAvailableModes())
                && is_numeric($themeId);
        }
        return $this->_isVdeRequest;
    }

    /**
     * Get available modes for Design Editor
     *
     * @return array
     */
    public function getAvailableModes()
    {
        return array(Mage_DesignEditor_Model_State::MODE_NAVIGATION);
    }

    /**
     * Returns the translation mode the current request is in (null, text, script, or alt).
     *
     * @return mixed
     */
    public function getTranslationMode()
    {
        return $this->_translationMode;
    }

    /**
     * Sets the translation mode for the current request (null, text, script, or alt);
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_DesignEditor_Helper_Data
     */
    public function setTranslationMode(Mage_Core_Controller_Request_Http $request)
    {
        $this->_translationMode = $request->getParam(self::TRANSLATION_MODE, null);
        return $this;
    }

    /**
     * Returns an indicator of whether or not inline translation is allowed in VDE.
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_translationMode !== null;
    }
}
