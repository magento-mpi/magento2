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
     * @var Saas_Limitation_Model_Limitation_Validator
     */
    protected $_limitationValidator;

    /**
     * @var Saas_Limitation_Model_Limitation_LimitationInterface
     */
    protected $_limitation;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Saas_Limitation_Model_Limitation_Validator $limitationValidator
     * @param Saas_Limitation_Model_Limitation_LimitationInterface $limitation
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Saas_Limitation_Model_Limitation_Validator $limitationValidator,
        Saas_Limitation_Model_Limitation_LimitationInterface $limitation,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_limitationValidator = $limitationValidator;
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
        return $this->_limitationValidator->exceedsThreshold($this->_limitation);
    }
}
