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

namespace Magento\Adminhtml\Block\Dashboard;

abstract class AbstractDashboard extends \Magento\Adminhtml\Block\Widget
{
    protected $_dataHelperName = null;

    /**
     * @var \Magento\Reports\Model\Resource\Order\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
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
