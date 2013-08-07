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
 * Landing page model
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Page extends Mage_Core_Model_Abstract
{
    /**
     * Prefix for model event names
     *
     * @var string
     */
    protected $_eventPrefix = 'launcher_page';

    /**
     * List of tiles associated with page
     *
     * @var Saas_Launcher_Model_Resource_Tile_Collection|null
     */
    protected $_tiles;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_init('Saas_Launcher_Model_Resource_Page');
    }

    /**
     * Retrieve collection of tiles associated with this page
     *
     * @return Saas_Launcher_Model_Resource_Tile_Collection|null
     */
    public function getTiles()
    {
        return $this->_tiles;
    }

    /**
     * Set collection of related tiles
     *
     * @param Saas_Launcher_Model_Resource_Tile_Collection|null $tiles
     * @return Saas_Launcher_Model_Page
     */
    public function setTiles(Saas_Launcher_Model_Resource_Tile_Collection $tiles)
    {
        $this->_tiles = $tiles;
        return $this;
    }

    /**
     * Load landing page by its code
     *
     * @param $code
     * @return Saas_Launcher_Model_Page
     */
    public function loadByPageCode($code)
    {
        return $this->load($code, 'page_code');
    }

    /**
     * Check if page is complete (i.e. all related tiles are complete)
     *
     * @return bool
     */
    public function isComplete()
    {
        $isComplete = true;
        foreach ($this->getTiles() as $tile) {
            if (!$tile->isComplete()) {
                $isComplete = false;
                break;
            }
        }
        return $isComplete;
    }
}
