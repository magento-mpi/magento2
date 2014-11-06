<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class ProductMediaAttributeManagementTest extends WebapiAbstract
{
    /**
     * @magentoApiDataFixture Magento/Catalog/_files/attribute_set_with_image_attribute.php
     */
    public function testGetList()
    {
        $attributeSetName = 'attribute_set_with_media_attribute';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/media/types/' . $attributeSetName,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ),
            'soap' => array(
                // @todo fix SOAP configuration after SOAP tests are functional
                'operation' => 'catalogProductAttributeMediaReadServiceV1Types'
            ),
        );

        $requestData = array(
            'attributeSetName' => $attributeSetName,
        );

        $mediaAttributes = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertNotEmpty($mediaAttributes);
        $attribute = $this->getAttributeByCode($mediaAttributes,  'funny_image');
        $this->assertNotNull($attribute);
        $this->assertEquals('Funny image', $attribute['frontend_label']);
        $this->assertEquals(1, $attribute['is_user_defined']);
    }

    /**
     * Retrieve attribute based on given attribute code
     *
     * @param array $attributeList
     * @param string $attributeCode
     * @return array|null
     */
    protected function getAttributeByCode($attributeList, $attributeCode)
    {
        foreach ($attributeList as $attribute) {
            if ($attributeCode == $attribute['attribute_code']) {
                return $attribute;
            }
        }

        return null;
    }
}
