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
    public function getPage()
    {
        $page = parent::getPage();
        if (!$page) {
            $pageCode = $this->getPageCode();
            //$page = Mage::getModel('Mage_Launcher_Model_Page')->loadByCode($pageCode);
            $page = Mage::getModel('Mage_Launcher_Model_Page')->load(1);
            $this->setPage($page);
        }
        return $page;
    }

    /**
     * Retrieve array of Tiles blocks
     *
     * @return array|null
     */
    public function getTiles()
    {
        $tilesBlocks = array();

        if (!$this->getPage()) {
             return $tilesBlocks;
        }

        /** @var $page Mage_Launcher_Model_Page */
        $page = $this->getPage();

        $tiles = $page->getTiles();


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
