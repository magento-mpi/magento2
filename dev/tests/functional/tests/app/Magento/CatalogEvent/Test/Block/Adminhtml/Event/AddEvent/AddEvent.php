<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Block\Adminhtml\Event\AddEvent;

use Magento\Backend\Test\Block\FormPageActions;

class AddEvent extends FormPageActions
{
    /**
     * "Add Event..." button
     *
     * @var string
     */
    protected $addEvent = '[data-ui-id="category-edit-form-add-event-button"]';

    /**
     * Click on "Add Event..." button
     */
    public function addEventNew()
    {
        $this->_rootElement->find($this->addEvent)->click();
    }
}
