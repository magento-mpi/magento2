<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\Tree;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

/**
 * @magentoApiDataFixture Magento/Catalog/_files/category_tree.php
 */
class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_WRITE_NAME = 'catalogCategoryTreeReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/categories';

    /**
     * @dataProvider treeDataProvider
     */
    public function testTree($rootCategoryId, $depth, $expectedLevel, $expectedId)
    {
        $requestData = ['rootCategoryId' => $rootCategoryId, 'depth' => $depth];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_WRITE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_WRITE_NAME . 'tree'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);

        for($i = 0; $i < $expectedLevel; $i++) {
            $result = $result['children'][0];
        }
        $this->assertEquals($expectedId, $result['id']);
        $this->assertEmpty($result['children']);
    }

    public function treeDataProvider()
    {
        return array(
            [2, 100, 3, 402],
            [2, null, 3, 402],
            [400, 1, 1, 401],
            [401, 0, 0, 401],
        );
    }
}
