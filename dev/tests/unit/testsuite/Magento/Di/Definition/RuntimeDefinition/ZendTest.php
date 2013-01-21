<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Di_Definition_RuntimeDefinition_ZendTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Class for test
     */
    const TEST_CLASS_NAME = 'stdClass';
    const TEST_CLASS_INSTANTIATOR = '__construct';
    /**#@-*/

    public function testGetInstantiator()
    {
        $generatorClass = $this->getMock('Magento_Di_Generator_Class');
        $generatorClass->expects($this->once())
            ->method('generateForConstructor')
            ->with(self::TEST_CLASS_NAME);

        $model = new Magento_Di_Definition_RuntimeDefinition_Zend(
            null,
            array(self::TEST_CLASS_NAME),
            $generatorClass
        );
        $this->assertEquals(self::TEST_CLASS_INSTANTIATOR, $model->getInstantiator(self::TEST_CLASS_NAME));
    }
}
