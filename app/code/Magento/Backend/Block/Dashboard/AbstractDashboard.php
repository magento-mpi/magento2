<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard tab abstract
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Dashboard;

abstract class AbstractDashboard extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Backend\Helper\Dashboard\AbstractDashboard
     */
    protected $_dataHelper = null;

    /**
     * @var \Magento\Reports\Model\Resource\Order\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    public function getCollection()
    {
        return $this->getDataHelper()->getCollection();
    }

    public function getCount()
    {
        return $this->getDataHelper()->getCount();
    }

    /**
     * Get data helper
     *
     * @return \Magento\Backend\Helper\Dashboard\AbstractDashboard
     */
    public function getDataHelper()
    {
        return $this->_dataHelper;
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
