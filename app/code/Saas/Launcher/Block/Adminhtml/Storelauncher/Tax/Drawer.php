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
 * Tax Drawer Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_Drawer extends Saas_Launcher_Block_Adminhtml_Drawer
{
    /**
     * Tax rule collection
     *
     * @var Magento_Tax_Model_Resource_Calculation_Rule_Collection
     */
    protected $_taxRuleCollection;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Saas_Launcher_Model_LinkTracker $linkTracker
     * @param Magento_Tax_Model_Resource_Calculation_Rule_Collection $taxRuleCollection
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Saas_Launcher_Model_LinkTracker $linkTracker,
        Magento_Tax_Model_Resource_Calculation_Rule_Collection $taxRuleCollection,
        array $data = array()
    ) {
        parent::__construct($context, $linkTracker, $data);
        // for now this collection is used only once so no cloning is needed before use
        $this->_taxRuleCollection = $taxRuleCollection;
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Saas_Launcher_Helper_Data')->__('Tax Rules');
    }

    /**
     * Check if 'Tax Rules Enabled' switcher is switched off (i.e. no tax rules exist and tile is complete)
     *
     * @return boolean
     */
    public function isUseTaxControlSwitchedOff()
    {
        return $this->getTile()->isComplete() && $this->getTaxRuleCount() == 0;
    }

    /**
     * Check if 'Tax Rules Enabled' switcher is disabled (i.e. tax rules exist and tile is complete)
     *
     * @return boolean
     */
    public function isUseTaxControlDisabled()
    {
        return $this->getTile()->isComplete() && $this->getTaxRuleCount() > 0;
    }

    /**
     * Retrieve the number of tax rules created in the system
     *
     * @return int
     */
    public function getTaxRuleCount()
    {
        return $this->_taxRuleCollection->getSize();
    }
}
