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
 * Tax tile system observer
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Tax_SystemObserver
{
    /**
     * @var Mage_Launcher_Model_TileFactory
     */
    protected $_tileFactory;

    /**
     * Class constructor
     *
     * @param Mage_Launcher_Model_TileFactory $tileFactory
     */
    public function __construct(Mage_Launcher_Model_TileFactory $tileFactory)
    {
        $this->_tileFactory = $tileFactory;
    }

    /**
     * Handle tax rule save (tax_rule_save_commit_after event)
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleTaxRuleSave(Varien_Event_Observer $observer)
    {
        // Successful save of any tax rule has to change the state of tax tile to complete
        // This logic is put directly into observer (without delegation to state resolver) because it is trivial
        $this->_tileFactory->create()->loadByCode('tax')->setState(Mage_Launcher_Model_Tile::STATE_COMPLETE)->save();
    }
}
