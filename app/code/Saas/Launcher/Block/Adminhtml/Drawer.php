<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base Drawer Block
 *
 * @method Saas_Launcher_Model_Tile getTile()
 * @method Saas_Launcher_Block_Adminhtml_Drawer setTile(Saas_Launcher_Model_Tile $value)
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Saas_Launcher_Block_Adminhtml_Drawer extends Mage_Backend_Block_Widget_Form
{
    /**
     * Display value for secret configuration parameters
     */
    const SECRET_DATA_DISPLAY_VALUE = '******';

    /**
     * @var Saas_Launcher_Model_LinkTracker
     */
    protected $_linkTracker;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Saas_Launcher_Model_LinkTracker $linkTracker
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Saas_Launcher_Model_LinkTracker $linkTracker,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_linkTracker = $linkTracker;
    }

    /**
     * Get Tile Code
     *
     * @throws Saas_Launcher_Exception
     * @return string
     */
    public function getTileCode()
    {
        $tile = $this->getTile();
        if (!isset($tile)) {
            throw new Saas_Launcher_Exception('Tile was not set.');
        }
        return $tile->getTileCode();
    }

    /**
     * Get Tile State
     *
     * @throws Saas_Launcher_Exception
     * @return int
     */
    public function getTileState()
    {
        $tile = $this->getTile();
        if (!isset($tile)) {
            throw new Saas_Launcher_Exception('Tile was not set.');
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
     * @return Saas_Launcher_Model_LinkTracker
     */
    public function getTrackerLink($route = '', $params = array())
    {
        $urlCode = md5($route . serialize($params));
        $linkTracker = clone $this->_linkTracker;
        $linkTracker->load($urlCode, 'code');
        if (!$linkTracker->getId()) {
            $linkTracker->setCode($urlCode);
            $linkTracker->setUrl($route);
            $linkTracker->setParams(serialize($params));
            $linkTracker->save();
        }
        return $linkTracker;
    }

    /**
     * Retrieve Store Config Flag
     *
     * @param string $path
     * @param mixed $store
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
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
