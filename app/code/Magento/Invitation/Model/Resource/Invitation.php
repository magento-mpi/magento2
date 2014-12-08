<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Model\Resource;

/**
 * Invitation data resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Invitation extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Intialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_invitation', 'invitation_id');
        $this->addUniqueField(
            ['field' => ['customer_id', 'email'], 'title' => __('Invitation for same email address')]
        );
    }

    /**
     * Save invitation tracking info
     *
     * @param int $inviterId
     * @param int $referralId
     * @return void
     */
    public function trackReferral($inviterId, $referralId)
    {
        $data = ['inviter_id' => (int)$inviterId, 'referral_id' => (int)$referralId];
        $this->_getWriteAdapter()->insertOnDuplicate(
            $this->getTable('magento_invitation_track'),
            $data,
            array_keys($data)
        );
    }
}
