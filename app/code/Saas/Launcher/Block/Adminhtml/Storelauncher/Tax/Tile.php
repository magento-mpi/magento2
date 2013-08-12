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
 * Tax Tile Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile extends Saas_Launcher_Block_Adminhtml_Tile
{
    /**
     * Tax rule collection
     *
     * @var Magento_Tax_Model_Resource_Calculation_Rule_Collection
     */
    protected $_taxRuleCollection;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Tax_Model_Resource_Calculation_Rule_Collection $taxRuleCollection
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Tax_Model_Resource_Calculation_Rule_Collection $taxRuleCollection,
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
