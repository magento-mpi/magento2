<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Logging\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Logging
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Logging extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Logging\Test\Repository\Logging';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Logging\Test\Handler\Logging\LoggingInterface';

    protected $defaultDataSet = [
    ];

    protected $log_id = [
        'attribute_code' => 'log_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $ip = [
        'attribute_code' => 'ip',
        'backend_type' => 'bigint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $x_forwarded_ip = [
        'attribute_code' => 'x_forwarded_ip',
        'backend_type' => 'bigint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $event_code = [
        'attribute_code' => 'event_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $time = [
        'attribute_code' => 'time',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $action = [
        'attribute_code' => 'action',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $info = [
        'attribute_code' => 'info',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $user = [
        'attribute_code' => 'user',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $user_id = [
        'attribute_code' => 'user_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $fullaction = [
        'attribute_code' => 'fullaction',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $error_message = [
        'attribute_code' => 'error_message',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $aggregated_information = [
        'attribute_code' => 'aggregated_information',
        'backend_type' => 'virtual',
    ];

    public function getLogId()
    {
        return $this->getData('log_id');
    }

    public function getIp()
    {
        return $this->getData('ip');
    }

    public function getXForwardedIp()
    {
        return $this->getData('x_forwarded_ip');
    }

    public function getEventCode()
    {
        return $this->getData('event_code');
    }

    public function getTime()
    {
        return $this->getData('time');
    }

    public function getAction()
    {
        return $this->getData('action');
    }

    public function getInfo()
    {
        return $this->getData('info');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getUser()
    {
        return $this->getData('user');
    }

    public function getUserId()
    {
        return $this->getData('user_id');
    }

    public function getFullaction()
    {
        return $this->getData('fullaction');
    }

    public function getErrorMessage()
    {
        return $this->getData('error_message');
    }

    public function getAggregatedInformation()
    {
        return $this->getData('aggregated_information');
    }
}
