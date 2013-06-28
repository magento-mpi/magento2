<?php
/**
 * Test Web API routing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class RoutingTest extends Magento_Test_TestCase_WebapiAbstract
{
    /**
     * TODO: Temporary test for test framework implementation phase
     */
    public function testBasicRouting()
    {
        $productId = 1;
        $serviceInfo = array(
            'soap' => array(
                'service' => 'catalogProduct',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductItem'
            )
        );
        $requestData = array('entity_id' => $productId);
        $product = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($productId, $product['entity_id'], "Product was retrieved unsuccessfully");
    }
}
