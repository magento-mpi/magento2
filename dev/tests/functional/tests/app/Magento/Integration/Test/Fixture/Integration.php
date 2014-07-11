<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Integration
 * Integration data fixture
 */
class Integration extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Integration\Test\Repository\Integration';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Integration\Test\Handler\Integration\IntegrationInterface';

    protected $defaultDataSet = [
        'name' => 'default_integration_%isolation%',
        'email' => 'test_%isolation%@example.com',
        'resource_access' => 'All',
    ];

    protected $integration_id = [
        'attribute_code' => 'integration_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'integration_info',
    ];

    protected $email = [
        'attribute_code' => 'email',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'integration_info',
    ];

    protected $endpoint = [
        'attribute_code' => 'endpoint',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'integration_info',
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $consumer_id = [
        'attribute_code' => 'consumer_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => 'CURRENT_TIMESTAMP',
        'input' => '',
    ];

    protected $updated_at = [
        'attribute_code' => 'updated_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '0000-00-00 00:00:00',
        'input' => '',
    ];

    protected $setup_type = [
        'attribute_code' => 'setup_type',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $identity_link_url = [
        'attribute_code' => 'identity_link_url',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'group' => 'integration_info',
    ];

    protected $resource_access = [
        'attribute_code' => 'resource_access',
        'backend_type' => 'virtual',
        'group' => 'api',
    ];

    protected $resources = [
        'attribute_code' => 'resources',
        'backend_type' => 'virtual',
        'group' => 'api',
    ];

    public function getIntegrationId()
    {
        return $this->getData('integration_id');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function getEndpoint()
    {
        return $this->getData('endpoint');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getConsumerId()
    {
        return $this->getData('consumer_id');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    public function getSetupType()
    {
        return $this->getData('setup_type');
    }

    public function getIdentityLinkUrl()
    {
        return $this->getData('identity_link_url');
    }

    public function getResourceAccess()
    {
        return $this->getData('resource_access');
    }

    public function getResources()
    {
        return $this->getData('resources');
    }
}
