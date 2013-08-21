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
 * Invitation status history collection
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Invitation_Model_Resource_Invitation_History_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Intialize collection
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Enterprise_Invitation_Model_Invitation_History',
            'Enterprise_Invitation_Model_Resource_Invitation_History'
        );
    }
}
