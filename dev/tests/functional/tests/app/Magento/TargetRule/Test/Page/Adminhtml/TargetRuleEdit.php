<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Page\Adminhtml;

use Mtf\Page\BackendPage; 

/**
 * Class TargetRuleEdit
 * Backend target rule edit page
 */
class TargetRuleEdit extends BackendPage
{
    const MCA = 'admin/targetrule/edit';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'targetRuleForm' => [
            'name' => 'targetRuleForm',
            'class' => 'Magento\TargetRule\Test\Block\Adminhtml\Targetrule\Edit\Form',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\TargetRule\Test\Block\Adminhtml\Targetrule\Edit\Form
     */
    public function getTargetRuleForm()
    {
        return $this->getBlockInstance('targetRuleForm');
    }
}
