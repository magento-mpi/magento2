<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Invitation status history resource model
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Invitation_Model_Resource_Invitation_History extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Intialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('magento_invitation_status_history', 'history_id');
    }
}
