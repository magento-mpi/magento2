<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class IntegrationNew
 */
class IntegrationNew extends BackendPage
{
    const MCA = 'admin/integration/new';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'formPageActions' => [
            'class' => 'Magento\Integration\Test\Block\Adminhtml\Integration\Edit\IntegrationFormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'integrationForm' => [
            'class' => 'Magento\Integration\Test\Block\Adminhtml\Integration\Edit\IntegrationForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Integration\Test\Block\Adminhtml\Integration\Edit\IntegrationFormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Integration\Test\Block\Adminhtml\Integration\Edit\IntegrationForm
     */
    public function getIntegrationForm()
    {
        return $this->getBlockInstance('integrationForm');
    }
}
