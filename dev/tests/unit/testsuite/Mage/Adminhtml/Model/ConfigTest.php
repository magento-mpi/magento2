<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Model_ConfigTest extends PHPUnit_Framework_TestCase
{

    /**
     * @param Varien_Simplexml_Element $xmlData
     * @param boolean $isSingleStoreMode
     * @param Varien_Simplexml_Element $node
     * @param string $website
     * @param string $store
     * @param mixed $expectedResult
     * @param string $message
     * @dataProvider addItemFilterDataProvider
     */
    public function testHasChildren($xmlData, $isSingleStoreMode, $node, $website, $store, $expectedResult, $message)
    {
        $app = $this->getMock('Mage_Core_Model_App', array('isSingleStoreMode'), array(), '', true);
        $app->expects($this->any())
            ->method('isSingleStoreMode')
            ->will($this->returnValue($isSingleStoreMode));

        $config = new Mage_Adminhtml_Model_Config(array(
            'data' => $xmlData,
            'app' => $app,
        ));
        $result = $config->hasChildren($node, $website, $store);
        $this->assertEquals($expectedResult, $result, $message);
    }

    public function addItemFilterDataProvider()
    {
        $data = file_get_contents(__DIR__ . '/_files/system.xml');
        $xmlData = new Varien_Simplexml_Element($data);
        return array(
            array($xmlData, false, $xmlData->sections->dev, null, null, true, 'Case 1'),
            array($xmlData, false, $xmlData->sections->dev->groups->css, null, null, true, 'Case 2'),
            array($xmlData, false, $xmlData->sections->dev->groups->css, 'base', null, true, 'Case 3'),
            array($xmlData, false, $xmlData->sections->dev->groups->css, 'base', 'default', true, 'Case 4'),
            array($xmlData, false, $xmlData->sections->dev->groups->debug, null, null, false, 'Case 5'),
            array($xmlData, false, $xmlData->sections->dev->groups->debug, 'base', null, true, 'Case 6'),
            array($xmlData, false, $xmlData->sections->dev->groups->debug, 'base', 'default', true, 'Case 7'),
            array($xmlData, false, $xmlData->sections->dev->groups->js, null, null, false, 'Case 8'),
            array($xmlData, false, $xmlData->sections->dev->groups->js, 'base', null, false, 'Case 9'),
            array($xmlData, false, $xmlData->sections->dev->groups->js, 'base', 'default', true, 'Case 10'),
            array($xmlData, true, $xmlData->sections->dev->groups->debug, null, null, true, 'Case 11'),
            array($xmlData, true, $xmlData->sections->dev->groups->debug, 'base', null, true, 'Case 12'),
            array($xmlData, true, $xmlData->sections->dev->groups->debug, 'base', 'default', true, 'Case 13'),
            array($xmlData, true, $xmlData->sections->dev->groups->js, null, null, true, 'Case 14'),
            array($xmlData, true, $xmlData->sections->dev->groups->js, 'base', null, true, 'Case 15'),
            array($xmlData, true, $xmlData->sections->dev->groups->js, 'base', 'default', true, 'Case 16'),
        );
    }
}
