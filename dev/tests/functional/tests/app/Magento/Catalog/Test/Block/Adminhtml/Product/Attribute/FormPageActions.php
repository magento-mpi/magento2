<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute;

use Magento\Backend\Test\Block\FormPageActions as AbstractFormPageActions;

/**
 * Class FormPageActions
 * Page Actions for Product Attribute
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
     * 'Delete Attribute' button
     *
     * @var string
     */
    protected $delete = '#delete';

    /**
     * Click on "Add New Attribute" button
     *
     * @return void
     */
    public function addProductAttribute()
    {
        $this->_rootElement->find($this->addNewAttribute)->click();
    }

    /**
     * Check 'Delete Attribute' button availability
     *
     * @return bool
     */
    public function checkDeleteAttributeButton()
    {
        return $this->_rootElement->find($this->delete)->isVisible();
    }
}
