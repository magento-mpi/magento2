<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base Drawer Block
 *
 * @method Mage_Launcher_Model_Tile getTile()
 * @method Mage_Launcher_Block_Adminhtml_Drawer setTile(Mage_Launcher_Model_Tile $value)
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Drawer extends Mage_Backend_Block_Widget_Form
{
    /**
     * Display value for secret configuration parameters
     */
    const SECRET_DATA_DISPLAY_VALUE = '******';

    /**
     * @var Mage_Launcher_Model_LinkTracker
     */
    protected $_linkTracker;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs,
     * @param Mage_Core_Model_Logger $logger,
     * @param Magento_Filesystem $filesystem,
     * @param Mage_Launcher_Model_LinkTracker $linkTracker
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem,
        Mage_Launcher_Model_LinkTracker $linkTracker,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $filesystem, $data
        );

        $this->_linkTracker = $linkTracker;
    }


    /**
     * Path to template file
     *
     * @todo Default template specified, but it should be changed to custom one
     * @var string
     */
    protected $_template = 'Mage_Backend::widget/form.phtml';

    /**
     * Get Tile Code
     *
     * @throws Mage_Launcher_Exception
     * @return string
     */
    public function getTileCode()
    {
        $tile = $this->getTile();
        if (!isset($tile)) {
            throw new Mage_Launcher_Exception('Tile was not set.');
        }
        return $tile->getCode();
    }

    /**
     * Get Tile State
     *
     * @throws Mage_Launcher_Exception
     * @return int
     */
    public function getTileState()
    {
        $tile = $this->getTile();
        if (!isset($tile)) {
            throw new Mage_Launcher_Exception('Tile was not set.');
        }
        return $tile->getState();
    }

    /**
     * Get Translated Tile Header
     *
     * @todo This function should get data from Tile model
     * @return string
     */
    public function getTileHeader()
    {
        //@TODO: This function should get data from Tile model
        return '';
    }

    /**
     * Get Response Content
     *
     * @return array
     */
    public function getResponseContent()
    {
        $responseContent = array(
            'success' => true,
            'error_message' => '',
            'tile_code' => $this->getTileCode(),
            'tile_state' => $this->getTileState(),
            'tile_content' => $this->toHtml(),
            'tile_header' => $this->getTileHeader(),
        );
        return $responseContent;
    }

    /**
     * Get link tracker object
     * @param string $route
     * @param array $params
     * @return Mage_Launcher_Model_LinkTracker
     */
    public function getTrackerLink($route = '', $params = array())
    {
        $urlCode = md5($route . serialize($params));
        $this->_linkTracker->unsetData();
        $this->_linkTracker->load($urlCode, 'code');
        if (!$this->_linkTracker->getId()) {
            $this->_linkTracker->setCode($urlCode);
            $this->_linkTracker->setUrl($route);
            $this->_linkTracker->setParams(serialize($params));
            $this->_linkTracker->save();
        }
        return $this->_linkTracker;
    }

    /**
     * Retrieve Store Config Flag
     *
     * @param string $path
     * @param mixed $store
     * @return boolean
     */
    public function getConfigFlag($path, $store = null)
    {
        return $this->_storeConfig->getConfigFlag($path, $store);
    }

    /**
     * Retrieve store configuration parameter
     *
     * @param string $path
     * @param mixed $store
     * @return string|null
     */
    public function getConfigValue($path, $store = null)
    {
        return $this->_storeConfig->getConfig($path, $store);
    }

    /**
     * Retrieve store configuration parameter (not-empty value is represented by asterisk sequence)
     *
     * @param string $configPath
     * @param mixed $store
     * @return string|null
     */
    public function getObscuredConfigValue($configPath, $store = null)
    {
        $value = $this->getConfigValue($configPath, $store);
        if (!empty($value)) {
            return self::SECRET_DATA_DISPLAY_VALUE;
        }
        return $value;
    }

    /**
     * Render additional content after drawer form
     *
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        $formInit = '<script type="text/javascript">jQuery("#drawer-form").mage("form").mage("validation");</script>';
        return $html . $formInit;
    }
}
