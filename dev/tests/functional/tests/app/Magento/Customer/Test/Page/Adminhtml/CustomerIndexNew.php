<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CustomerIndexNew
 *
 * @package Magento\Customer\Test\Page\Adminhtml
 */
class CustomerIndexNew extends BackendPage
{
    const MCA = 'customer/index/new/';

    protected $_blocks = [
        'editForm' => [
            'name' => 'editForm',
            'class' => 'Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\Edit\CustomerForm
     */
    public function getEditForm()
    {
        return $this->getBlockInstance('editForm');
    }
}
