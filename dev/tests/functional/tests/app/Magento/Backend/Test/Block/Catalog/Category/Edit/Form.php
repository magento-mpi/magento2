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

namespace Magento\Backend\Test\Block\Catalog\Category\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Category container block
 *
 * @package Magento\Backend\Test\Block\Catalog\Category\Edit
 */
class Form extends FormTabs
{
    /**
     * Custom tab classes for product form
     *
     * @var array
     */
    protected $_tabClasses = array(
        'category_info_tabs_group_4' => '\\Magento\\Backend\\Test\\Block\\Catalog\\Category\\Tab\\Attributes'
    );

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->saveButton = '[data-ui-id=category-edit-form-save-button]';
    }

    public function getCategoryId()
    {
        $idField = $this->_rootElement->find('group_4id', Locator::SELECTOR_ID);
        return $idField->getValue();
    }
}
