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
    Mtf\Page\Page;

class Config extends Page
{
    /**
     * Url
     */
    const MCA = 'admin/system_config';

    /**
     * Config Edit form selector
     *
     * @var string
     */
    protected $form = '#config-edit-form';

    /**
     * Store switcher selector
     *
     * @var string
     */
    protected $storeSwitcher = '#store_switcher';

    /**
     * Page actions selector
     *
     * @var string
     */
    protected $pageActions = '.page-actions';

    /**
     * Messages selector
     *
     * @var string
     */
    protected $messages = '#messages';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Retrieve form block
     *
     * @return \Magento\Backend\Test\Block\System\Config\Form
     */
    public function getForm()
    {
        return Factory::getBlockFactory()->getMagentoBackendSystemConfigForm($this->_browser->find($this->form));
    }

    /**
     * Retrieve store switcher block
     *
     * @return Switcher
     */
    public function getStoreSwitcher()
    {
        return Factory::getBlockFactory()->getMagentoBackendSystemConfigSwitcher(
            $this->_browser->find($this->storeSwitcher, Locator::SELECTOR_CSS, 'select')
        );
    }

    /**
     * Retrieve page actions block
     *
     * @return PageActions
     */
    public function getActions()
    {
        return Factory::getBlockFactory()->getMagentoBackendPageActions($this->_browser->find($this->pageActions));
    }

    /**
     * Retrieve messages block
     *
     * @return Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages($this->_browser->find($this->messages));
    }
}
