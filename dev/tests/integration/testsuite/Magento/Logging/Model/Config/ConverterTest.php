<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Config;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DOMDocument
     */
    protected $_loggingDom;

    /**
     * @var \Magento\Logging\Model\Config\Converter
     */
    protected $_converter;

    public function setUp()
    {
        $this->_loggingDom = new \DOMDocument();
        $this->_loggingDom->load(__DIR__ . '/_files/logging.xml');
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_converter = $objectManager->get('Magento\Logging\Model\Config\Converter');
    }

    /**
     * @param string $actionName
     * @param array $expectedResult
     * @dataProvider convertDataProvider
     */
    public function testConvert($actionName, $expectedResult)
    {
        $result = $this->_converter->convert($this->_loggingDom);
        $this->assertEquals($expectedResult, $result['logging']['enterprise_checkout']['actions'][$actionName]);
    }

    /**
     * @return array
     */
    public function convertDataProvider()
    {
        return array(
            array(
                'adminhtml_customersegment_match',
                array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'refresh_data',
                    'controller_action' => 'adminhtml_customersegment_match',
                    'post_dispatch' => 'Enterprise_CustomerSegment_Model_Logging::postDispatchCustomerSegmentMatch'
                )
            ),
            array(
                'customer_index_save',
                array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'save',
                    'controller_action' => 'customer_index_save',
                    'expected_models' => array(
                        'Enterprise_CustomerBalance_Model_Balance' => array(),
                        '@' => array('extends' => 'merge')
                    ),
                    'skip_on_back' => array('adminhtml_customerbalance_form', 'customer_index_edit')
                )
            )
        );
    }
}
