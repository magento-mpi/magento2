<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class TaxRateNew
 */
class TaxRateNew extends BackendPage
{
    const MCA = 'tax/rate/add';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'formPageActions' => [
            'class' => 'Magento\Tax\Test\Block\Adminhtml\Rate\Edit\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'taxRateForm' => [
            'class' => 'Magento\Tax\Test\Block\Adminhtml\Rate\Edit\Form',
            'locator' => '#rate-form',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Tax\Test\Block\Adminhtml\Rate\Edit\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Tax\Test\Block\Adminhtml\Rate\Edit\Form
     */
    public function getTaxRateForm()
    {
        return $this->getBlockInstance('taxRateForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
