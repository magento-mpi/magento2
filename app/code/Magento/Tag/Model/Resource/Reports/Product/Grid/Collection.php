<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Products Tags grid collection
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Reports_Product_Grid_Collection
    extends Magento_Tag_Model_Resource_Reports_Product_Collection
{
    /**
     * @var Magento_Tag_Model_Tag
     */
    protected $_model;

    /**
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Tag_Model_Tag $tagModel
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Tag_Model_Tag $tagModel
    ) {
        $this->_model = $tagModel;
        parent::__construct($catalogData, $catalogProductFlat, $fetchStrategy);
    }

    /**
     * @return Magento_Tag_Model_Resource_Product_Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addUniqueTagedCount()
            ->addAllTagedCount()
            ->addStatusFilter($this->_model->getApprovedStatus())
            ->addGroupByProduct();
        return $this;

    }
}
