<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product price block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{
    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * @return Mage_Core_Model_StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
