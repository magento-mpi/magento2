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
 * Landing Page Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Page extends Mage_Backend_Block_Template
{
    /**
     * Page Code
     *
     * @var string
     */
    protected $_pageCode = '';

    /**
     * @var Saas_Launcher_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * Page Model
     *
     * @var Saas_Launcher_Model_Page
     */
    protected $_page;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Saas_Launcher_Model_PageFactory $pageFactory
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Saas_Launcher_Model_PageFactory $pageFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_pageFactory = $pageFactory;
    }

    /**
     * Retrieve Landing Page model
     *
     * @return Saas_Launcher_Model_Page
     */
    public function getPage()
    {
        if (!$this->_page) {
            $this->_page = $this->_pageFactory->create()->loadByPageCode($this->_pageCode);
        }
        return $this->_page;
    }

    /**
     * Set Page Model
     *
     * @param $page Saas_Launcher_Model_Page
     * @return Saas_Launcher_Block_Adminhtml_Page
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

        foreach ($tiles as $item) {
            $block = $this->getLayout()->getBlock($this->_getTileBlockName($item->getTileCode()));
            if (!$block) {
                /** @var $block Saas_Launcher_Block_Adminhtml_Tile */
                $block = $this->getLayout()->createBlock('Saas_Launcher_Block_Adminhtml_Tile',
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
