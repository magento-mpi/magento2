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
class Mage_Launcher_Block_LandingPage extends Mage_Backend_Block_Abstract
{
    /**
     * Page Code
     *
     * @var string
     */
    protected $_pageCode = '';

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
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );

        if (isset($data['page'])) {
            $this->_page = $data['page'];
            $this->_pageCode = $this->_page->getCode();
        } else {
            $this->_page = Mage::getModel('Mage_Launcher_Model_Page')->loadByCode($this->_pageCode);
        }
    }

    /**
     * Retrieve Landing Page model
     *
     * @return Mage_Launcher_Model_Page
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * Retrieve array of Tiles blocks
     *
     * @return array|null
     */
    public function getTiles()
    {
        $tilesBlocks = array();

        if (!$this->getPage() || !$this->getPage()->getTiles()) {
             return $tilesBlocks;
        }
        $tiles = $this->getPage()->getTiles();

        foreach($tiles as $item) {
            $block = $this->getLayout()->getBlock($this->_getTileBlockName($item->getCode()));
            if (!$block) {
                /** @var $block Mage_Launcher_Block_Tile */
                $block = $this->getLayout()->createBlock('Mage_Launcher_Block_Tile',
                    $this->_getTileBlockName($item->getCode()));
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
