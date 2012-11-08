<?php
/**
* {license_notice}
*
* @category    Mage
* @package     Mage_Reports
* @copyright   {copyright}
* @license     {license_link}
*/


/**
* Customers New Accounts Report collection
*
* @category    Mage
* @package     Mage_Reports
* @author      Magento Core Team <core@magentocommerce.com>
*/
class Mage_Reports_Model_Resource_Customer_Totals_Initial extends Mage_Reports_Model_Resource_Report_Collection
{
    /*
     * Report subcollection class name
     * @var Mage_Reports_Model_Resource_Customer_Totals_Collection  $_reportCollectionClass
     */
    protected $_reportCollectionClass = 'Mage_Reports_Model_Resource_Customer_Totals_Collection';
}
