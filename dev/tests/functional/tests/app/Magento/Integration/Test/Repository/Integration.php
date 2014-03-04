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
    const ALL_INTEGRATION_TABS = 'api_tab';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        $this->_data[self::INTEGRATION_TAB] = $this->_getIntegrationTabData();
        $this->_data[self::ALL_INTEGRATION_TABS] = array_replace_recursive($this->_getIntegrationTabData(), $this->_getApiTabData());
    }

    /**
     * Get data for integration fixture tab.
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

    /**
     * Get data for api fixture tab.
     *
     * @return array
     */
    protected function _getApiTabData()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'resource_access' => array(
                        'value' => 'Custom',
                        'input_value' => '0',
                        'group' => 'integration_edit_tabs_api_section'
                    ),
                    'resources' => array(
                        'value' => array('Dashboard'),
                        'group' => 'integration_edit_tabs_api_section'
                    ),
                )
            )
        );
    }
}
