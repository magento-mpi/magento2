<?php
/**
 * Created by PhpStorm.
 * User: orykh
 * Date: 14.04.14
 * Time: 18:14
 */

namespace Magento\Customer\Test\Block\Adminhtml\Edit;

use Magento\Backend\Test\Block\Widget\Form as AbstractForm;
use Mtf\Client\Element\Locator;

class Form extends AbstractForm
{
    /**
     * Customer group name
     *
     * @var string
     */
    protected $customerGroupName = '_accountgroup_id';

    /**
     * @param $groupName
     * @return bool
     */
    public function customerGroupNameFind($groupName)
    {
        return $this->_rootElement->find(
            "//*[@id='{$this->customerGroupName}']/option[contains(.,'{$groupName}')]",
            Locator::SELECTOR_XPATH,
            'option'
        )->isVisible();
    }
}
