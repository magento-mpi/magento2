<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\AdminHtml\Page;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element\Locator;

/**
 * Class Edit
 * Backend Cms Page edit page
 *
 * @package Magento\Cms\Test\Block\AdminHtml\Page
 */
class Edit extends FormTabs
{
    /**
     * Product toggle button
     *
     * @var string
     */
    protected $toggleButton = "//button[@id='togglepage_content']";

    /**
     * Toggle Editor button
     *
     */
    public function toggleEditor()
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_XPATH)->click();
    }
}
