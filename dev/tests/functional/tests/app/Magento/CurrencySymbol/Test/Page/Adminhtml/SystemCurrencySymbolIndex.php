<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class SystemCurrencySymbolIndex
 *
 * @package Magento\CurrencySymbol\Test\Page\Adminhtml
 */
class SystemCurrencySymbolIndex extends BackendPage
{
    const MCA = 'admin/system_currencysymbol/index';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'currencySymbolForm' => [
            'name' => 'currencySymbolForm',
            'class' => 'Magento\CurrencySymbol\Test\Block\Adminhtml\System\CurrencySymbolForm',
            'locator' => '.grid',
            'strategy' => 'css selector',
        ],
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\CurrencySymbol\Test\Block\Adminhtml\System\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\CurrencySymbol\Test\Block\Adminhtml\System\CurrencySymbolForm
     */
    public function getCurrencySymbolForm()
    {
        return $this->getBlockInstance('currencySymbolForm');
    }

    /**
     * @return \Magento\CurrencySymbol\Test\Block\Adminhtml\System\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }
}
