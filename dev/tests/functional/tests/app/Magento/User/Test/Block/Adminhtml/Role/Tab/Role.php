<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\Role\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;

class Role extends Tab
{
    /**
     * Fills username in user grid
     *
     * @param array $fields
     * @param Element $element
     * @return void
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $this->getUserGrid()->searchAndOpen(['username' => $fields['username']]);
    }

    /**
     * Returns user grid block
     *
     * @return \Magento\User\Test\Block\Adminhtml\Role\Tab\User\Grid
     */
    public function getUserGrid()
    {
        return $this->blockFactory->create(
            'Magento\User\Test\Block\Adminhtml\Role\Tab\User\Grid',
            ['element' => $this->_rootElement->find('#roleUserGrid')]
        );
    }
}
