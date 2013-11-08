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

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
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
                        'value' => 'email_%isolation%@example.com',
                        'group' => 'integration_edit_tabs_info_section_content'
                    ),
                    'authentication' => array(
                        'value' => 'Manual',
                        'input_value' => 2,
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
                        'value' => 'email_%isolation%@example.com',
                        'group' => 'integration_edit_tabs_info_section_content'
                    ),
                    'authentication' => array(
                        'value' => 'OAuth',
                        'input_value' => 1,
                        'group' => 'integration_edit_tabs_info_section_content',
                        'input' => 'select'
                    ),
                    'endpoint' => array(
                        'value' => 'http://example.com/%isolation%',
                        'group' => 'integration_edit_tabs_info_section_content'
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
    protected function _getXssIntegrationDataAll()
    {
        $xssData = $this->_getOauthIntegrationData();
        $xssData['data']['fields']['name']['value'] = "<script>alert('XSS')</script>";
        $xssData['data']['fields']['email']['value'] = "<script>alert('XSS')</script>";
        $xssData['data']['fields']['endpoint']['value'] = "<script>alert('XSS')</script>";
        return $xssData;
    }

    /**
     * Generate data for integration fixture with oAuth tokens exchange.
     *
     * @return array
     */
    protected function _getXssIntegrationData()
    {
        $xssData = $this->_getOauthIntegrationData();
        $xssData['data']['fields']['name']['value'] = "<script>alert('XSS')</script>";
        $xssData['data']['fields']['endpoint']['value'] = "<script>alert('XSS')</script>";
        return $xssData;
    }
}
