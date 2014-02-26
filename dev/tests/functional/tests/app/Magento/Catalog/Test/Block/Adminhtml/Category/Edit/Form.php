<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Category\Edit;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Form
 * Category container block
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Category\Edit
 */
class Form extends FormTabs
{
    /**
     * Save button
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id=category-edit-form-save-button]';

    /**
     * Get category id
     *
     * @return array|string
     */
    public function getCategoryId()
    {
        $idField = $this->_rootElement->find('group_4id', Locator::SELECTOR_ID);
        return $idField->getValue();
    }
}
