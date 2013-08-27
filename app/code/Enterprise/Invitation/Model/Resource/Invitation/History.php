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
 * Invitation status history resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Invitation_Model_Resource_Invitation_History extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Intialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_invitation_status_history', 'history_id');
    }
}
