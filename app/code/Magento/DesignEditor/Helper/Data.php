<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Helper;

use Magento\App\Helper\Context;

/**
 * Design Editor main helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * XML path to VDE front name setting
     *
     * @var string
     */
    protected $_frontName;

    /**
     * XML path to VDE disabled cache type setting
     *
     * @var array
     */
    protected $_disabledCacheTypes;

    /**
     * Parameter to indicate the translation mode (null, text, script, or alt).
     */
    const TRANSLATION_MODE = "translation_mode";

    /**
     * @var bool
     */
    protected $_isVdeRequest = false;

    /**
     * @var string
     */
    protected $_translationMode;

    /**
     * @param Context $context
     * @param string $frontName
     * @param array $disabledCacheTypes
     */
    public function __construct(
        Context $context,
        $frontName,
        array $disabledCacheTypes = array()
    ) {
        parent::__construct($context);
        $this->_frontName = $frontName;
        $this->_disabledCacheTypes = $disabledCacheTypes;
    }

    /**
     * Get VDE front name prefix
     *
     * @return string
     */
    public function getFrontName()
    {
        return $this->_frontName;
    }

    /**
     * Get disabled cache types in VDE mode
     *
     * @return array
     */
    public function getDisabledCacheTypes()
    {
        return $this->_disabledCacheTypes;
    }

    /**
     * This method returns an indicator of whether or not the current request is for vde
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     */
    public function isVdeRequest(\Magento\App\RequestInterface $request = null)
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
     * @param \Magento\App\RequestInterface $request
     * @return $this
     */
    public function setTranslationMode(\Magento\App\RequestInterface $request)
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
