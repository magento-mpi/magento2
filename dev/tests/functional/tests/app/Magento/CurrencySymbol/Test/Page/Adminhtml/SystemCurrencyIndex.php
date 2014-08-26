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
 * Class SystemCurrencyIndex
 */
class SystemCurrencyIndex extends BackendPage
{
    const MCA = 'admin/system_currency/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'gridPageActions' => [
            'class' => 'Magento\CurrencySymbol\Test\Block\Adminhtml\System\Currency\GridPageActions',
            'locator' => '.grid-actions',
            'strategy' => 'css selector',
        ],
        'mainPageActions' => [
            'class' => 'Magento\CurrencySymbol\Test\Block\Adminhtml\System\Currency\MainPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CurrencySymbol\Test\Block\Adminhtml\System\Currency\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\CurrencySymbol\Test\Block\Adminhtml\System\Currency\MainPageActions
     */
    public function getMainPageActions()
    {
        return $this->getBlockInstance('mainPageActions');
    }
}
