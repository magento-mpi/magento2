<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Customers Detail Tags grid collection
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Resource_Reports_Customer_Detail_Collection extends Mage_Tag_Model_Resource_Product_Collection
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(
        Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Mage_Core_Controller_Request_Http $request
    ) {
        $this->_request = $request;
        parent::__construct($fetchStrategy);
    }

    /**
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return Mage_Tag_Model_Resource_Product_Collection|Mage_Tag_Model_Resource_Reports_Customer_Detail_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->joinAttribute('original_name', 'catalog_product/name', 'entity_id')->addCustomerFilter($this
            ->getRequest()->getParam('id'))->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)->addStoresVisibility()
            ->setActiveFilter()->addGroupByTag()->setRelationId();
        return $this;
    }
}
