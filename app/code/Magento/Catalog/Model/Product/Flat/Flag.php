<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Calatog Product Flat Flag Model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Flat_Flag extends Magento_Core_Model_Flag
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
    public function getFlagData()
    {
        $flagData = parent::getFlagData();
        if (!is_array($flagData)) {
            $flagData = array();
            $this->setFlagData($flagData);
        }
        return $flagData;
    }

    /**
     * Retrieve Catalog Product Flat Data is built flag
     *
     * @return bool
     */
    public function getIsBuilt()
    {
        $flagData = $this->getFlagData();
        if (!isset($flagData['is_built'])) {
            $flagData['is_built'] = false;
            $this->setFlagData($flagData);
        }
        return (bool)$flagData['is_built'];
    }

    /**
     * Set Catalog Product Flat Data is built flag
     *
     * @param bool $flag
     *
     * @return Magento_Catalog_Model_Product_Flat_Flag
     */
    public function setIsBuilt($flag)
    {
        $flagData = $this->getFlagData();
        $flagData['is_built'] = (bool)$flag;
        $this->setFlagData($flagData);
        return $this;
    }

    /**
     * Set Catalog Product Flat Data is built flag
     *
     * @deprecated after 1.7.0.0 use Magento_Catalog_Model_Product_Flat_Flag::setIsBuilt() instead
     *
     * @param bool $flag
     *
     * @return Magento_Catalog_Model_Product_Flat_Flag
     */
    public function setIsBuild($flag)
    {
        $this->setIsBuilt($flag);
        return $this;
    }
}
