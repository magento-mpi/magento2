<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Service\V1\DownloadableLink;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Model\Rest\Config as RestConfig;


class ReadServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    public function testGetListForAbsentProduct()
    {
        $sku = 'absent-product' . time();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $sku . '/downloadable-links',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'downloadableDownloadableLinkReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'downloadableDownloadableLinkReadServiceV1GetList'
            ]
        ];

        $requestData = ['productSku' => $sku];

        $expectedMessage = 'Requested product doesn\'t exist';
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch(\SoapFault $e) {
            $this->assertEquals($expectedMessage, $e->getMessage());
        } catch(\Exception $e) {
            $this->assertContains($expectedMessage, $e->getMessage());
        }
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGetListForSimpleProduct()
    {
        $sku = 'simple';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $sku . '/downloadable-links',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'downloadableDownloadableLinkReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'downloadableDownloadableLinkReadServiceV1GetList'
            ]
        ];

        $requestData = ['productSku' => $sku];

        $list = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEmpty($list);
    }

    /**
     * @magentoApiDataFixture Magento/Downloadable/_files/product_with_files.php
     */
    public function testGetList()
    {
        $sku = 'downloadable-product';

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $sku . '/downloadable-links',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'downloadableDownloadableLinkReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'downloadableDownloadableLinkReadServiceV1GetList'
            ]
        ];

        $requestData = ['productSku' => $sku];

        $list = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertNotEmpty($list);
    }
}
