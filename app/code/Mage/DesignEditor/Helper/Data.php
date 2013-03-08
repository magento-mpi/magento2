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
    const XML_PATH_DEFAULT_HANDLE       = 'vde/design_editor/defaultHandle';
    const XML_PATH_DISABLED_CACHE_TYPES = 'vde/design_editor/disabledCacheTypes';
    const XML_PATH_BLOCK_WHITE_LIST     = 'vde/design_editor/block/white_list';
    const XML_PATH_BLOCK_BLACK_LIST     = 'vde/design_editor/block/black_list';
    const XML_PATH_CONTAINER_WHITE_LIST = 'vde/design_editor/container/white_list';
    const XML_PATH_DAYS_TO_EXPIRE       = 'vde/design_editor/layout_update/days_to_expire';
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
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config $configuration
     * @internal param \Mage_Core_Model_Translate $translator
     */
    public function __construct(Mage_Core_Helper_Context $context, Mage_Core_Model_Config $configuration)
    {
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
     * Get VDE default handle name
     *
     * @return string
     */
    public function getDefaultHandle()
    {
        return (string)$this->_configuration->getNode(self::XML_PATH_DEFAULT_HANDLE);
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
     * Get list of configuration element values
     *
     * @param string $xmlPath
     * @return array
     */
    protected function _getElementsList($xmlPath)
    {
        $elements = array();
        $node = $this->_configuration->getNode($xmlPath);
        if ($node) {
            $data = $node->asArray();
            if (is_array($data)) {
                $elements = array_values($data);
            }
        }
        return $elements;
    }

    /**
     * Get list of allowed blocks
     *
     * @return array
     */
    public function getBlockWhiteList()
    {
        return $this->_getElementsList(self::XML_PATH_BLOCK_WHITE_LIST);
    }

    /**
     * Get list of not allowed blocks
     *
     * @return array
     */
    public function getBlockBlackList()
    {
        return $this->_getElementsList(self::XML_PATH_BLOCK_BLACK_LIST);
    }

    /**
     * Get list of allowed blocks
     *
     * @return array
     */
    public function getContainerWhiteList()
    {
        return $this->_getElementsList(self::XML_PATH_CONTAINER_WHITE_LIST);
    }

    /**
     * Get expiration days count
     *
     * @return string
     */
    public function getDaysToExpire()
    {
        return (int)$this->_configuration->getNode(self::XML_PATH_DAYS_TO_EXPIRE);
    }

    /**
     * @return bool
     */
    public function isVdeRequest()
    {
        return $this->_isVdeRequest;
    }

    /**
     * @param $isVdeRequest
     */
    public function setVdeRequest($isVdeRequest)
    {
        $this->_isVdeRequest = $isVdeRequest;
    }

    /**
     * Returns the translation mode the current request is in (null, text, script, or alt).
     * @return mixed
     */
    public function getTranslationMode()
    {
        return $this->_translationMode;
    }

    /**
     * Sets the translation mode for the current request (null, text, script, or alt);
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function setTranslationMode(Mage_Core_Controller_Request_Http $request)
    {
        $this->_translationMode = $request->getParam(self::TRANSLATION_MODE, null);
    }

    /**
     * Get current handle url
     *
     * @return string
     */
    public function getCurrentHandleUrl()
    {
        /** @var $objectManager Magento_ObjectManager */
        $objectManager = Mage::getObjectManager();
        /** @var $vdeUrlModel Mage_DesignEditor_Model_Url_Handle */
        $vdeUrlModel = $objectManager->get('Mage_DesignEditor_Model_Url_Handle');
        $handle = $objectManager->get('Mage_Backend_Model_Session')->
            getData(Mage_DesignEditor_Model_State::CURRENT_HANDLE_SESSION_KEY);
        if (empty($handle)) {
            $handle = 'default';
        }
        return $vdeUrlModel->getUrl('design/page/type', array('handle' => $handle));
    }
}
