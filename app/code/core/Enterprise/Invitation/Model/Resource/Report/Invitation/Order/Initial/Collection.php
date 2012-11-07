<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports invitation order report collection
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Invitation_Model_Resource_Report_Invitation_Order_Initial_Collection
    extends Mage_Reports_Model_Resource_Report_Collection
{
    protected $_reportCollectionClass = 'Enterprise_Invitation_Model_Resource_Report_Invitation_Order_Collection';
}
