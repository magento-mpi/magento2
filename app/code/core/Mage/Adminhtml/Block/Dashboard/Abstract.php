<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard tab abstract
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

abstract class Mage_Adminhtml_Block_Dashboard_Abstract extends Mage_Adminhtml_Block_Widget
{
    protected $_dataHelperName = null;

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
