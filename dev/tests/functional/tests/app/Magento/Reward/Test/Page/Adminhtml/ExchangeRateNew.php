<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class ExchangeRateNew
 */
class ExchangeRateNew extends BackendPage
{
    const MCA = 'admin/reward_rate/new';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'orderStatusForm' => [
            'name' => 'orderStatusForm',
            'class' => 'Magento\Reward\Test\Block\Adminhtml\Reward\Rate\Edit\Form',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Reward\Test\Block\Adminhtml\Reward\Rate\Edit\Form
     */
    public function getOrderStatusForm()
    {
        return $this->getBlockInstance('orderStatusForm');
    }
}
