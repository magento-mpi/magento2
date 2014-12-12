<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
