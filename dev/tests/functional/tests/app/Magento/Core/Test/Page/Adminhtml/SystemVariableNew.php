<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class SystemVariableNew
 */
class SystemVariableNew extends BackendPage
{
    const MCA = 'admin/system_variable/new';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'systemVariableForm' => [
            'name' => 'systemVariableForm',
            'class' => 'Magento\Core\Test\Block\Adminhtml\SystemVariable\Edit\Form',
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
     * @return \Magento\Core\Test\Block\Adminhtml\SystemVariable\Edit\Form
     */
    public function getSystemVariableForm()
    {
        return $this->getBlockInstance('systemVariableForm');
    }
}
