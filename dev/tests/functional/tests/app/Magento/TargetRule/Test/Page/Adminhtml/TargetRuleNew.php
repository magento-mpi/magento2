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
 * Class TargetRuleNew
 */
class TargetRuleNew extends BackendPage
{
    const MCA = 'admin/targetrule/new';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'targetRuleForm' => [
            'class' => 'Magento\TargetRule\Test\Block\Adminhtml\Targetrule\Edit\TargetRuleForm',
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
     * @return \Magento\TargetRule\Test\Block\Adminhtml\Targetrule\Edit\TargetRuleForm
     */
    public function getTargetRuleForm()
    {
        return $this->getBlockInstance('targetRuleForm');
    }
}
