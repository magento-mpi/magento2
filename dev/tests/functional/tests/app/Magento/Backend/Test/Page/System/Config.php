<?php
/**
 * Store configuration form page
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page\System;
use Magento\Backend\Test\Block\PageActions;
use Magento\Backend\Test\Block\System\Config\Switcher;
use Magento\Core\Test\Block\Messages;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory,
    Mtf\Page\Page,
    \Magento\Backend\Test\Block\System\Config\Form;

class Config extends Page
{
    const MCA = 'admin/system_config';

    /**
     * @var Form
     */
    protected $_form;

    /**
     * @var Switcher
     */
    protected $_storeSwitcher;

    /**
     * @var PageActions
     */
    protected $_pageActions;

    /**
     * @var Messages
     */
    protected $_messages;

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_form = Factory::getBlockFactory()->getMagentoBackendSystemConfigForm(
            $this->_browser->find('#config-edit-form')
        );

        $this->_storeSwitcher = Factory::getBlockFactory()->getMagentoBackendSystemConfigSwitcher(
            $this->_browser->find('#store_switcher', Locator::SELECTOR_CSS, 'select')
        );

        $this->_pageActions = Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );

        $this->_messages = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages')
        );
    }

    /**
     * Retrieve form block
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Retrieve store switcher block
     *
     * @return Switcher
     */
    public function getStoreSwitcher()
    {
        return $this->_storeSwitcher;
    }

    /**
     * Retrieve page actions block
     *
     * @return PageActions
     */
    public function getActions()
    {
        return $this->_pageActions;
    }

    /**
     * Retrieve messages block
     *
     * @return Messages
     */
    public function getMessagesBlock()
    {
        return $this->_messages;
    }
}
