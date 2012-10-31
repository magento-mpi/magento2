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
 * Adminhtml customers by orders report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Customer_Orders_Grid extends Mage_Reports_Block_Adminhtml_Grid
{

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('Mage_Reports_Model_Resource_Customer_Orders_Collection');
    }
}
