<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Page;

use Magento\Integration\Test\Block\Backend\IntegrationForm;
use Mtf\Factory\Factory;

/**
 * Edit integration page.
 */
class AdminIntegrationEdit extends AdminIntegrationNew
{
    /**
     * URL for edit integration page.
     */
    const MCA = 'admin/integration/edit';

    /**
     * {@inheritdoc}
     */
    public function open(array $params = array())
    {
        throw new \LogicException("Please use openByName() instead.");
    }

    /**
     * Open existing integration page by integration name.
     *
     * @param string $integrationName
     */
    public function openByName($integrationName)
    {
        $integrationGridPage = Factory::getPageFactory()->getAdminIntegration();
        $integrationGridPage->open();
        $integrationGridPage->getGridBlock()->searchAndOpen(array('name' => $integrationName));
    }
}
