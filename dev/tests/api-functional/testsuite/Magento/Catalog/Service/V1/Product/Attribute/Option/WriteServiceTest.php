<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductAttributeServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/attributes';

    public function testAddOption()
    {
        $objectManager = Bootstrap::getObjectManager();
        $testAttributeCode = 'color';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $testAttributeCode . '/options',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'addOption'
            ],
        ];

        /** @var \Magento\Catalog\Service\V1\Data\Eav\OptionBuilder $optionBuilder */
        $optionBuilder = $objectManager->get('\Magento\Catalog\Service\V1\Data\Eav\OptionBuilder');
        /** @var \Magento\Catalog\Service\V1\Data\Eav\Option\LabelBuilder $labelBuilder */
        $labelBuilder = $objectManager->get('\Magento\Catalog\Service\V1\Data\Eav\Option\LabelBuilder');

        $optionBuilder->setLabel('new color');
        $optionBuilder->setDefault(true);
        $optionBuilder->setStoreLabels(
            [
                $labelBuilder->populateWithArray(['label' => 'DE label', 'store_id' => 1])->create(),
            ]
        );

        $response = $this->_webApiCall(
            $serviceInfo,
            [
                'id' => $testAttributeCode,
                'option' => $optionBuilder->create()->__toArray(),
            ]
        );

        $this->assertTrue($response);
    }
} 