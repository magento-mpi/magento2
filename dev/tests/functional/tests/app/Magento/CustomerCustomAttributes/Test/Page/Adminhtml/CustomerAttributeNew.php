<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CustomerAttributeNew
 */
class CustomerAttributeNew extends BackendPage
{
    const MCA = 'admin/customer_attribute/new';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'formPageActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'customerCustomAttributesForm' => [
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\Edit\CustomerCustomAttributesForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\Edit\CustomerCustomAttributesForm
     */
    public function getCustomerCustomAttributesForm()
    {
        return $this->getBlockInstance('customerCustomAttributesForm');
    }
}
