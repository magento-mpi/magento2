<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Page;

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
}
