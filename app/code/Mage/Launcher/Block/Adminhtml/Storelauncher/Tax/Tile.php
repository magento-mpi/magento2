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
 * Tax Tile Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile extends Mage_Launcher_Block_Adminhtml_Tile
{
    /**
     * Tax rule collection
     *
     * @var Mage_Tax_Model_Resource_Calculation_Rule_Collection
     */
    protected $_taxRuleCollection;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Tax_Model_Resource_Calculation_Rule_Collection $taxRuleCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Tax_Model_Resource_Calculation_Rule_Collection $taxRuleCollection,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        // for now this collection is used only once so no cloning is needed before use
        $this->_taxRuleCollection = $taxRuleCollection;
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
