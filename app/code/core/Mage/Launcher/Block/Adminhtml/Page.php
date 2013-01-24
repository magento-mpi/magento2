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
 * Landing Page Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Page extends Mage_Backend_Block_Abstract
{
    /**
     * Page Code
     *
     * @var string
     */
    protected $_pageCode = '';

    /**
     * @var Mage_Launcher_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * Page Model
     *
     * @var Mage_Launcher_Model_Page
     */
    protected $_page;

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
        Mage_Launcher_Model_PageFactory $pageFactory,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $filesystem, $data
        );
        $this->_pageFactory = $pageFactory;
    }

    /**
     * Retrieve Landing Page model
     *
     * @return Mage_Launcher_Model_Page
     */
    public function getPage()
    {
        if (!$this->_page) {
            $this->_page = $this->_pageFactory->create()->loadByPageCode($this->_pageCode);
        }
        return $this->_page;
    }

    /**
     * Sets Page
     *
     * @param $page Mage_Launcher_Model_Page
     * @return Mage_Launcher_Block_LandingPage
     */
    public function setPage($page)
    {
        $this->_page = $page;
        return $this;
    }

    /**
     * Retrieve array of Tiles blocks
     *
     * @return array|null
     */
    public function getTileBlocks()
    {
        $tilesBlocks = array();

        if (!$this->getPage() || !$this->getPage()->getTiles()) {
             return $tilesBlocks;
        }
        $tiles = $this->getPage()->getTiles();

        foreach($tiles as $item) {
            $block = $this->getLayout()->getBlock($this->_getTileBlockName($item->getTileCode()));
            if (!$block) {
                /** @var $block Mage_Launcher_Block_Adminhtml_Tile */
                $block = $this->getLayout()->createBlock('Mage_Launcher_Block_Adminhtml_Tile',
                    $this->_getTileBlockName($item->getTileCode()));
            }
            $block->setTile($item);
            $tilesBlocks[] = $block;
        }

        return $tilesBlocks;
    }

    /**
     * Build Tile Block name by Tile Code
     *
     * @param string $code
     * @return string
     */
    protected function _getTileBlockName($code)
    {
        return $code . '.tile';
    }
}
