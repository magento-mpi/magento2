<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Grid row url id  generator
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Widget_Grid_Row_UrlGeneratorCustomerOnline
    implements  Mage_Backend_Model_Widget_Grid_Row_Interface
{
    /**
     * Create url for passed item using passed url model
     * @param Varien_Object $item
     * @return string
     */
    public function getUrl($item)
    {
        return  ($this->_authorization->isAllowed('Mage_Customer::manage') && $item->getCustomerId())
        ? $this->getUrl('*/customer/edit', array('id' => $item->getCustomerId())) : '';
    }
}
