<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml\Group\Edit;

use Magento\Backend\Test\Block\Widget\Form as AbstractForm;
use Mtf\Client\Element\Locator;

class Form extends AbstractForm
{
    /**
     * Customer group name
     *
     * @var string
     */
    protected $customerGroupName = '#customer_group_code';

    /**
     * Customer tax class
     *
     * @var string
     */
    protected $customerGroupTaxClass = '#tax_class_id';

    /**
     * Main form customer grid
     * @var string
     */
    protected $customerGroupMainForm = '[id="page:main-container"]';

    /**
     * Fill options for the group
     *
     * @param array $customerOptions
     */
    public function fillOptions($customerOptions)
    {
    }
}
