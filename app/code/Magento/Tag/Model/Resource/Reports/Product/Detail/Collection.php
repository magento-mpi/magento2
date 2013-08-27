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
 * Report Tags Product Detail submitted grid collection
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Reports_Product_Detail_Collection
    extends Magento_Tag_Model_Resource_Reports_Product_Collection
{
    /**
     * @var Magento_Tag_Model_Tag
     */
    protected $_model;

    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Magento_Catalog_Helper_Product_Flat $catalogProductFlat
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Tag_Model_Tag $tagModel
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Flat $catalogProductFlat,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Tag_Model_Tag $tagModel,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_model = $tagModel;
        $this->_request = $request;
        parent::__construct($catalogData, $catalogProductFlat, $fetchStrategy);
    }

    /**
     * @return Magento_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return Magento_Tag_Model_Resource_Product_Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addTagedCount()->addProductFilter($this->getRequest()->getParam('id'))
            ->addStatusFilter($this->_model->getApprovedStatus())->addStoresVisibility()->setActiveFilter()
            ->addGroupByTag()->setRelationId();
        return $this;
    }
}
