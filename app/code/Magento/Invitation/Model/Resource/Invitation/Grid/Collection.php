<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Invitation\Model\Resource\Invitation\Grid;

class Collection extends \Magento\Invitation\Model\Resource\Invitation\Collection
{
    /**
     * Join website ID and referrals information (email)
     *
     * @return \Magento\Invitation\Model\Resource\Invitation\Collection|\Magento\Invitation\Model\Resource\Invitation\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsiteInformation()->addInviteeInformation();
        return $this;
    }
}
