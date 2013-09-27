<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard tab abstract
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

abstract class Magento_Adminhtml_Block_Dashboard_Abstract extends Magento_Adminhtml_Block_Widget
{
    protected $_dataHelperName = null;

    /**
     * @var Magento_Reports_Model_Resource_Order_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Reports_Model_Resource_Order_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Reports_Model_Resource_Order_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $data);
    }

    public function getCollection()
    {
           return $this->getDataHelper()->getCollection();
    }

    public function getCount()
    {
           return $this->getDataHelper()->getCount();
    }

    public function getDataHelper()
    {
           return $this->helper($this->getDataHelperName());
    }

    public  function getDataHelperName()
    {
           return $this->_dataHelperName;
    }

    public  function setDataHelperName($dataHelperName)
    {
           $this->_dataHelperName = $dataHelperName;
           return $this;
    }

    protected function _prepareData()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        $this->_prepareData();
        return parent::_prepareLayout();
    }
}
