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
 * Shipping carrier table rate grid block
 * WARNING: This grid used for export table rates
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Shipping\Carrier\Tablerate;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Website filter
     *
     * @var int
     */
    protected $_websiteId;

    /**
     * Condition filter
     *
     * @var string
     */
    protected $_conditionName;

    /**
     * Define grid properties
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('shippingTablerateGrid');
        $this->_exportPageSize = 10000;
    }

    /**
     * Set current website
     *
     * @param int $websiteId
     * @return \Magento\Adminhtml\Block\Shipping\Carrier\Tablerate\Grid
     */
    public function setWebsiteId($websiteId)
    {
        $this->_websiteId = \Mage::app()->getWebsite($websiteId)->getId();
        return $this;
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if (is_null($this->_websiteId)) {
            $this->_websiteId = \Mage::app()->getWebsite()->getId();
        }
        return $this->_websiteId;
    }

    /**
     * Set current website
     *
     * @param int $websiteId
     * @return \Magento\Adminhtml\Block\Shipping\Carrier\Tablerate\Grid
     */
    public function setConditionName($name)
    {
        $this->_conditionName = $name;
        return $this;
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    public function getConditionName()
    {
        return $this->_conditionName;
    }

    /**
     * Prepare shipping table rate collection
     *
     * @return \Magento\Adminhtml\Block\Shipping\Carrier\Tablerate\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Shipping\Model\Resource\Carrier\Tablerate\Collection */
        $collection = \Mage::getResourceModel('Magento\Shipping\Model\Resource\Carrier\Tablerate\Collection');
        $collection->setConditionFilter($this->getConditionName())
            ->setWebsiteFilter($this->getWebsiteId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare table columns
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('dest_country', array(
            'header'    => __('Country'),
            'index'     => 'dest_country',
            'default'   => '*',
        ));

        $this->addColumn('dest_region', array(
            'header'    => __('Region/State'),
            'index'     => 'dest_region',
            'default'   => '*',
        ));

        $this->addColumn('dest_zip', array(
            'header'    => __('Zip/Postal Code'),
            'index'     => 'dest_zip',
            'default'   => '*',
        ));

        $label = \Mage::getSingleton('Magento\Shipping\Model\Carrier\Tablerate')
            ->getCode('condition_name_short', $this->getConditionName());
        $this->addColumn('condition_value', array(
            'header'    => $label,
            'index'     => 'condition_value',
        ));

        $this->addColumn('price', array(
            'header'    => __('Shipping Price'),
            'index'     => 'price',
        ));

        return parent::_prepareColumns();
    }
}
