<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Ogone_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test protected method, which converts Magento internal charset (UTF-8) to the one, understandable
     * by Ogone (ISO-8859-1), and then encodes html entities
     */
    public function testTranslate()
    {
        /* Compose the string, which, when converted to ISO-8859-1, still looks like a valid UTF-8 string.
           So that the latter result of htmlentities() is different, depending on the encoding used for it. */
        $sourceString = 'Ë£';

        // Test protected method via reflection
        $config = $this->getMock('Magento_Ogone_Model_Config', array(), array(), '', false);
        $object = new Magento_Ogone_Model_Api($config);
        $method = new ReflectionMethod('Magento_Ogone_Model_Api', '_translate');
        $method->setAccessible(true);
        $result = $method->invoke($object, $sourceString);

        $this->assertEquals('&Euml;&pound;', $result);
    }
}
