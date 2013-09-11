<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Editor main helper
 */
namespace Magento\DesignEditor\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
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
     * @var \Magento\Core\Model\Config
     */
    protected $_configuration;

    /**
     * @var bool
     */
    protected $_isVdeRequest = false;

    /**
     * @var string
     */
    protected $_translationMode;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Config $configuration
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Config $configuration
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
     * This method returns an indicator of whether or not the current request is for vde
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @return bool
     */
    public function isVdeRequest(\Magento\Core\Controller\Request\Http $request = null)
    {
        if (null !== $request) {
            $result = false;
            $splitPath = explode('/', trim($request->getOriginalPathInfo(), '/'));
            if (count($splitPath) >= 3) {
                list($frontName, $currentMode, $themeId) = $splitPath;
                $result = $frontName === $this->getFrontName() && in_array($currentMode, $this->getAvailableModes())
                    && is_numeric($themeId);
            }
            $this->_isVdeRequest = $result;
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
        return array(\Magento\DesignEditor\Model\State::MODE_NAVIGATION);
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
     * @param \Magento\Core\Controller\Request\Http $request
     * @return \Magento\DesignEditor\Helper\Data
     */
    public function setTranslationMode(\Magento\Core\Controller\Request\Http $request)
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
