<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test if custom attributes are serialized correctly
 */
namespace Magento\Webapi\Routing;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModule1\Service\V1\Entity\ItemBuilder;
use Magento\Webapi\Model\Rest\Config as RestConfig;

class CustomAttributeSerializationTest extends \Magento\Webapi\Routing\BaseService
{
    /**
     * @var string
     */
    protected $_version;
    /**
     * @var string
     */
    protected $_restResourcePath;
    /**
     * @var string
     */
    protected $_soapService = 'testModule1AllSoapAndRest';

    /**
     * @var \Magento\Framework\Service\Data\Eav\AttributeValueBuilder
     */
    protected $valueBuilder;

    /**
     * @var ItemBuilder
     */
    protected $itemBuilder;

    /**
     * @var \Magento\TestModule1\Service\V1\Entity\CustomAttributeNestedDataObjectBuilder
     */
    protected $customAttributeNestedDataObjectBuilder;

    /**
     * @var \Magento\TestModule1\Service\V1\Entity\CustomAttributeDataObjectBuilder
     */
    protected $customAttributeDataObjectBuilder;

    /**
     * Set up custom attribute related data objects
     */
    protected function setUp()
    {
        $this->_version = 'V1';
        $this->_soapService = 'testModule1AllSoapAndRestV1';
        $this->_restResourcePath = "/{$this->_version}/testmodule1/";

        $this->valueBuilder = Bootstrap::getObjectManager()->create(
            'Magento\Framework\Service\Data\Eav\AttributeValueBuilder'
        );

        $this->itemBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModule1\Service\V1\Entity\ItemBuilder'
        );

        $this->customAttributeNestedDataObjectBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModule1\Service\V1\Entity\CustomAttributeNestedDataObjectBuilder'
        );
        $this->customAttributeDataObjectBuilder = Bootstrap::getObjectManager()->create(
            'Magento\TestModule1\Service\V1\Entity\CustomAttributeDataObjectBuilder'
        );
    }

    /**
     *  Test get item with any type
     */
    public function testNestedCustomAttributes()
    {
        $customAttributeNestedDataObject = $this->customAttributeNestedDataObjectBuilder
            ->setName('nestedNameValue')
            ->create();

        $customAttributeDataObject = $this->customAttributeDataObjectBuilder
            ->setName('nameValue')
            ->setCustomAttribute('custom_attribute_nested', $customAttributeNestedDataObject)
            ->setCustomAttribute('custom_attribute_int', 1)
            ->create();

        $item = $this->itemBuilder
            ->setItemId(1)
            ->setName('testProductAnyType')
            ->setCustomAttribute('custom_attribute_data_object', $customAttributeDataObject)
            ->setCustomAttribute('custom_attribute_string', 'someStringValue')
            ->create();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => $this->_restResourcePath . 'itemAnyType',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => ['service' => $this->_soapService, 'operation' => $this->_soapService . 'ItemAnyType']
        ];
        $requestData = $item->__toArray();
        $result = $this->_webApiCall($serviceInfo, ['item' => $requestData]);

        //\Magento\TestModule1\Service\V1\AllSoapAndRest::itemAnyType just return the input data back as response
        $this->assertEquals($requestData, $result);
    }

} 