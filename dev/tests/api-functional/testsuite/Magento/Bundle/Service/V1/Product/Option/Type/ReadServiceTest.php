<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option\Type;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'bundleProductOptionTypeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/bundle-products/option/types';

    public function testGetTypes()
    {
        $expected = [
            ['label' => 'Drop-down', 'code' => 'select'],
            ['label' => 'Radio Buttons', 'code' => 'radio'],
            ['label' => 'Checkbox', 'code' => 'checkbox'],
            ['label' => 'Multiple Select', 'code' => 'multi']
        ];
        $result = $this->getTypes();

        $this->assertEquals($expected, $result);
    }

    /**
     * @return string
     */
    protected function getTypes()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getTypes'
            ]
        ];
        return $this->_webApiCall($serviceInfo);
    }}
