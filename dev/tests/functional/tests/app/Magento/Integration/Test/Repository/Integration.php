<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Repository for integrations data.
 */
class Integration extends AbstractRepository
{
    const INTEGRATION_TAB = 'integration_tab';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        $this->_data[self::INTEGRATION_TAB] = $this->_getIntegrationTabData();
    }

    /**
     * Generate data for integration fixture tab.
     *
     * @return array
     */
    protected function _getIntegrationTabData()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array(
                        'value' => 'Integration_%isolation%',
                        'group' => 'integration_edit_tabs_info_section'
                    ),
                    'email' => array(
                        'value' => 'email_%isolation%@example.com',
                        'group' => 'integration_edit_tabs_info_section'
                    ),
                    'callback_url' => array(
                        'value' => 'http://example.com/%isolation%',
                        'group' => 'integration_edit_tabs_info_section'
                    )
                )
            )
        );
    }
}
