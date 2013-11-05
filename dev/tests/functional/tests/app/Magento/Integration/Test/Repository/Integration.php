<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Repository;

use Mtf\Repository\AbstractRepository;
use Magento\Integration\Model\Integration as IntegrationModel;

/**
 * Repository for integrations data.
 */
class Integration extends AbstractRepository
{
    const INTEGRATION_MANUAL = 'manual_integration';
    const INTEGRATION_OAUTH = 'oauth_integration';

    /** @var array */
    protected $_authOptions;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $authenticationSource = new \Magento\Integration\Model\Integration\Source\Authentication();
        $this->_authOptions = $authenticationSource->toOptionArray();
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        $this->_data[self::INTEGRATION_MANUAL] = $this->_getManualIntegrationData();
        $this->_data[self::INTEGRATION_OAUTH] = $this->_getOauthIntegrationData();
    }

    /**
     * Generate data for integration fixture with manual tokens exchange.
     *
     * @return array
     */
    protected function _getManualIntegrationData()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array(
                        'value' => 'Manual_Integration_%isolation%',
                        'group' => 'integration_edit_tabs_info_section_content'
                    ),
                    'email' => array(
                        'value' => 'email_%isolation%@null.com',
                        'group' => 'integration_edit_tabs_info_section_content'
                    ),
                    'authentication' => array(
                        'value' => $this->_authOptions[IntegrationModel::AUTHENTICATION_MANUAL],
                        'input_value' => IntegrationModel::AUTHENTICATION_MANUAL,
                        'group' => 'integration_edit_tabs_info_section_content',
                        'input' => 'select'
                    ),
                )
            )
        );
    }

    /**
     * Generate data for integration fixture with oAuth tokens exchange.
     *
     * @return array
     */
    protected function _getOauthIntegrationData()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array(
                        'value' => 'Oauth_Integration_%isolation%',
                        'group' => 'integration_edit_tabs_info_section_content'
                    ),
                    'email' => array(
                        'value' => 'email_%isolation%@null.com',
                        'group' => 'integration_edit_tabs_info_section_content'
                    ),
                    'authentication' => array(
                        'value' => $this->_authOptions[IntegrationModel::AUTHENTICATION_OAUTH],
                        'input_value' => IntegrationModel::AUTHENTICATION_OAUTH,
                        'group' => 'integration_edit_tabs_info_section_content',
                        'input' => 'select'
                    ),
                    'endpoint' => array(
                        'value' => 'http://endpoint.com',
                        'group' => 'integration_edit_tabs_info_section_content'
                    ),
                )
            )
        );
    }
}
