<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\OptionValue;

use Magento\TestFramework\TestCase\WebapiAbstract;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductCustomOptionsOptionValueReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/:productSku/options/:optionId/values';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     */
    public function testGetList()
    {
        $productSku = 'simple';

        $serviceInfo = $this->getListServiceInfo();

        $productOptions = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('\Magento\Catalog\Model\ProductRepository')->get($productSku)->getOptions();
        $option = array_shift($productOptions);

        $serviceInfo['rest']['resourcePath'] = str_replace(
            array(':productSku', ':optionId'), array($productSku, $option->getId()),
            $serviceInfo['rest']['resourcePath']
        );

        $optionValueData = $this->_webApiCall(
            $serviceInfo, ['productSku' => $productSku, 'optionId' => $option->getId()]
        );

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            foreach ($optionValueData as $key => $data) {
                $optionValueData[$key]['customAttributes'] = $data['custom_attributes'];
                $optionValueData[$key]['price'] = intval($optionValueData[$key]['price']);
                unset($optionValueData[$key]['custom_attributes']);
            }
        }

        $expectedOptions = include "/../_files/product_options.php";
        $expectedOptionData = array_shift($expectedOptions)['value'];

        $this->assertEquals($expectedOptionData, $optionValueData);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     * @expectedException \Exception
     */
    public function testGetListWithWrongOption()
    {
        $productSku = 'simple';
        $optionId = 'a';
        $serviceInfo = $this->getListServiceInfo();

        $serviceInfo['rest']['resourcePath'] = str_replace(
            array(':productSku', ':optionId'), array($productSku, $optionId),
            $serviceInfo['rest']['resourcePath']
        );

        $this->_webApiCall(
            $serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]
        );
    }

    protected function getListServiceInfo()
    {
        return [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getList'
            ]
        ];
    }
}
