<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Category;

use Magento\Backend\Test\Block\FormPageActions as AbstractFormPageActions;

/**
 * Class FormPageActions
 * Page Actions for Catalog Event
 */
class FormPageActions extends AbstractFormPageActions
{
    /**
     * "Add Event..." button
     *
     * @var string
     */
    protected $addEvent = '[data-ui-id="category-edit-form-add-event-button"]';

    /**
     * Click on "Add Event..." button
     *
     * @return void
     */
    public function addCatalogEvent()
    {
        $this->_rootElement->find($this->addEvent)->click();
    }
}
