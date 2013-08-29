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
 * Report Customers Detail Tags grid collection
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Reports_Customer_Detail_Collection
    extends Magento_Tag_Model_Resource_Product_Collection
{
    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_request = $request;
        parent::__construct($catalogProductFlat, $catalogData, $fetchStrategy);
    }

    /**
     * @return Magento_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return Magento_Tag_Model_Resource_Product_Collection|Magento_Tag_Model_Resource_Reports_Customer_Detail_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->joinAttribute('original_name', 'catalog_product/name', 'entity_id')->addCustomerFilter($this
            ->getRequest()->getParam('id'))->addStatusFilter(Magento_Tag_Model_Tag::STATUS_APPROVED)->addStoresVisibility()
            ->setActiveFilter()->addGroupByTag()->setRelationId();
        return $this;
    }
}
