<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Model\Resource\Invitation;

/**
 * Invitation status history resource model
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Intialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_invitation_status_history', 'history_id');
    }
}
