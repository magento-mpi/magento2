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
     * @var Mage_Launcher_Model_LinkTrackerFactory
     */
    protected $_linkTrackerFactory;

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
        Magento_Filesystem $filesystem,
        Mage_Launcher_Model_LinkTrackerFactory $linkTrackerFactory,
        array $data = array()
    ) {
        $this->_linkTrackerFactory = $linkTrackerFactory;

        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $filesystem, $data
        );
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
        $link = $this->_linkTrackerFactory->create();
        $link->load($urlCode, 'code');
        if (!$link->getId()) {
            $link->setCode($urlCode);
            $link->setUrl($route);
            $link->setParams(serialize($params));
            $link->save();
        }
        return $link;
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
