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
    extends Magento_Reports_Model_Resource_Report_Collection
{
    /*
     * Report sub-collection class name
     * @var string
     */
    protected $_reportCollection = 'Enterprise_Invitation_Model_Resource_Report_Invitation_Order_Collection';
}
