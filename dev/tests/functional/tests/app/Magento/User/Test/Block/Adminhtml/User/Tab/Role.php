<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\User\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;

/**
 * Class Role
 */
class Role extends Tab
{
    /**
     * Fill user options
     *
     * @param array $fields
     * @param Element|null $element
     * @return void
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $this->getRoleGrid()->searchAndOpen(['role_name' => $fields['role_name']]);
    }

    /**
     * @return \Magento\User\Test\Block\Adminhtml\User\Tab\Role\Grid
     */
    public function getRoleGrid()
    {
        return $this->blockFactory->create(
            'Magento\User\Test\Block\Adminhtml\User\Tab\Role\Grid',
            ['element' => $this->_rootElement->find('#permissionsUserRolesGrid')]
        );
    }
} 