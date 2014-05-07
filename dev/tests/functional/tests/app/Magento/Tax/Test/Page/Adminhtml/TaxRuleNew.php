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
 * Class TaxRuleNew
 *
 * @package Magento\Tax\Test\Page\Adminhtml
 */
class TaxRuleNew extends BackendPage
{
    const MCA = 'tax/rule/new';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'taxRuleForm' => [
            'name' => 'taxRuleForm',
            'class' => 'Magento\Tax\Test\Block\Adminhtml\Rule\Edit\Form',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'messageBlock' => [
            'name' => 'messageBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
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
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Edit\Form
     */
    public function getTaxRuleForm()
    {
        return $this->getBlockInstance('taxRuleForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }
}
