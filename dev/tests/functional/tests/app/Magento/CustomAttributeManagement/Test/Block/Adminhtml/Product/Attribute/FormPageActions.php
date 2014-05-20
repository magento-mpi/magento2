<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomAttributeManagement\Test\Block\Adminhtml\Product\Attribute;

use Magento\Backend\Test\Block\FormPageActions as AbstractFormPageActions;

/**
 * Class FormPageActions
 * Page Actions for Custom Attribute Management
 */
class FormPageActions extends AbstractFormPageActions
{
    /**
     * "Add New Attribute" button
     *
     * @var string
     */
    protected $addNewAttribute = '.add';

    /**
     * Click on "Add New Attribute" button
     *
     * @return void
     */
    public function addProductAttribute()
    {
        $this->_rootElement->find($this->addNewAttribute)->click();
    }
}
