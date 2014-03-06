<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\User\Edit;

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Form
 * Form for User Edit/Create page
 *
 * @package Magento\User\Test\Block\User\Edit
 */
class Form extends FormTabs
{
    /**
     * Role tab id
     *
     * @var string
     */
    protected $roleTab = 'page_tabs_roles_section';

    /**
     * Open Role tab for User Edit page
     */
    public function openRoleTab()
    {
        $this->_rootElement->find($this->roleTab, Locator::SELECTOR_ID)->click();
    }
}

