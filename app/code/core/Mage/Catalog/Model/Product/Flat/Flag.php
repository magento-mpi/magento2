<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Calatog Product Flat Flag Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Flat_Flag extends Mage_Core_Model_Flag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'catalog_product_flat';

    /**
     * Retrieve flag data array
     *
     * @return array
     */
    public function getFlagData() {
        $flagData = parent::getFlagData();
        if (!is_array($flagData)) {
            $flagData = array();
            $this->setFlagData($flagData);
        }
        return $flagData;
    }

    /**
     * Retrieve Catalog Product Flat is built flag
     *
     * @return bool
     */
    public function getIsBuilt() {
        $flagData = $this->getFlagData();
        if (!isset($flagData['is_built'])) {
            $flagData['is_built'] = false;
            $this->setFlagData($flagData);
        }
        return (bool)$flagData['is_built'];
    }

    /**
     * Set Catalog Product Flat is built flag
     *
     * @param bool $flag
     * @return Mage_Catalog_Model_Product_Flat_Flag
     */
    public function setIsBuild($flag) {
        $flagData = $this->getFlagData();
        $flagData['is_built'] = (bool)$flag;
        $this->setFlagData($flagData);
        return $this;
    }
}
