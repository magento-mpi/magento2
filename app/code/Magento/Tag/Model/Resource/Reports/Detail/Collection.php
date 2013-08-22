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
 * Report Popular Tags Details grid collection
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Reports_Detail_Collection
    extends Magento_Tag_Model_Resource_Reports_Customer_Collection
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
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Tag_Model_Tag $tagModel
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Tag_Model_Tag $tagModel,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_model = $tagModel;
        $this->_request = $request;
        parent::__construct($fetchStrategy);
    }

    /**
     * @return Magento_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }
    /**
     * @return Magento_Tag_Model_Resource_Customer_Collection|Magento_Tag_Model_Resource_Reports_Customer_Grid_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addStatusFilter($this->_model->getApprovedStatus())
            ->addTagFilter($this->getRequest()->getParam('id'))
            ->addProductToSelect();
        return $this;
    }
}
