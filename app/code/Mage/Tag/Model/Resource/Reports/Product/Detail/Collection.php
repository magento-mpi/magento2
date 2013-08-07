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
 * Report Tags Product Detail submitted grid collection
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Resource_Reports_Product_Detail_Collection
    extends Mage_Tag_Model_Resource_Reports_Product_Collection
{
    /**
     * @var Mage_Tag_Model_Tag
     */
    protected $_model;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Mage_Tag_Model_Tag $tagModel
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Mage_Tag_Model_Tag $tagModel,
        Mage_Core_Controller_Request_Http $request
    ) {
        $this->_model = $tagModel;
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
     * @return Mage_Tag_Model_Resource_Product_Collection|void
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
