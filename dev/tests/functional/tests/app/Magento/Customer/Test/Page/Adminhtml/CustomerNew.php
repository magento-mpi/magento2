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
 * Class CustomerNew
 *
 * @package Adminhtml
 */
class CustomerNew extends BackendPage
{
    const MCA = 'customer/index/new/';

    protected $_blocks = [
        'mainForm' => [
            'name' => 'mainForm',
            'class' => 'Magento\Customer\Test\Block\Adminhtml\Edit\Form',
            'locator' => '#_accountbase_fieldset',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Customer\Test\Block\Adminhtml\Edit\Form
     */
    public function getMainForm()
    {
        return $this->getBlockInstance('mainForm');
    }
}
