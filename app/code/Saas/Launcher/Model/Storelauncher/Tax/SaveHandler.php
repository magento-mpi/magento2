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
 * Save handler for Tax Tile
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Storelauncher_Tax_SaveHandler extends Saas_Launcher_Model_Tile_MinimalSaveHandler
{
    /**
     * Tax rule prototype
     *
     * @var Magento_Tax_Model_Calculation_Rule
     */
    protected $_taxRule;

    /**
     * Constructor
     *
     * @param Magento_Tax_Model_Calculation_Rule $taxRule
     */
    public function __construct(Magento_Tax_Model_Calculation_Rule $taxRule)
    {
        $this->_taxRule = $taxRule;
    }

    /**
     * Save function handle the whole Tile save process
     *
     * @param array $data Request data
     */
    public function save(array $data)
    {
        $preparedData = $this->prepareData($data);
        if (!empty($data['use_tax'])) {
            $this->_taxRule->setData($preparedData);
            $this->_taxRule->save();
        }
    }
}

