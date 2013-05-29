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
 * Product Tile Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile extends Saas_Launcher_Block_Adminhtml_Tile
{
    /**
     * @var Mage_Catalog_Model_Product_Limitation
     */
    protected $_limitation;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Catalog_Model_Product_Limitation $limitation
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Catalog_Model_Product_Limitation $limitation,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_limitation = $limitation;
    }

    /**
     * Retrieve the number of products created in the system
     *
     * @return int
     */
    public function getProductCount()
    {
        return $this->getTile()->getStateResolver()->getProductCount();
    }

    /**
     * Get Tile State
     *
     * @throws Saas_Launcher_Exception
     * @return int
     */
    public function getTileState()
    {
        $this->getTile()->refreshState();
        return parent::getTileState();
    }

    /**
     * Check whether adding a product is restricted
     *
     * @return bool
     */
    public function isAddProductRestricted()
    {
        return $this->_limitation->isCreateRestricted();
    }
}
