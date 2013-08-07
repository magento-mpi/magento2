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
 * Invitation status history collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Invitation_Model_Resource_Invitation_History_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Intialize collection
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento_Invitation_Model_Invitation_History',
            'Magento_Invitation_Model_Resource_Invitation_History'
        );
    }
}
