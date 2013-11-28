<?php
/**
 * Store creation page
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Page\System\Store;

use Mtf\Factory\Factory,
    Mtf\Client\Element\Locator,
    Mtf\Page\Page,
    \Magento\Backend\Test\Block\PageActions;

class NewStore extends Page
{
    const MCA = 'admin/system_store/newStore';

    /**
     * Store edit form block
     *
     * @var string
     */
    protected $formBlock = '#edit_form';

    /**
     * Page actions block
     *
     * @var string
     */
    protected $actionsBlock = '.page-actions';

    /**
     * Initialize page
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Retrieve form block
     *
     * @return \Magento\Backend\Test\Block\System\Store\Edit
     */
    public function getFormBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendSystemStoreEdit(
            $this->_browser->find($this->formBlock)
        );
    }

    /**
     * Retrieve actions block
     *
     * @return PageActions
     */
    public function getPageActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendPageActions($this->_browser->find($this->actionsBlock));
    }
}
