<?php
/**
 * Test Array_Converter functionality
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Varien_Convert_ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider mappingDataProvider
     */
    public function testMappingData($data, $expectedData)
    {
        $converter = new Varien_Convert_Object();
        $convertedData = $converter->convertDataToArray($data);
        $this->assertEquals($expectedData, $convertedData);
    }

    public function testMappingDataCycleDetected()
    {
        $objectA = new Varien_Object(array('keyA' => 'valueA'));
        $objectB = new Varien_Object(array('keyB' => 'valueB', 'object' => $objectA));
        $objectA->setObject($objectB);
        $converter = new Varien_Convert_Object();
        $expectedData = array(
            array('keyA'   => 'valueA',
                  'object' => array('keyB'   => 'valueB',
                                    'object' => Varien_Convert_Object::CYCLE_DETECTED_MARK)));
        $this->assertEquals($expectedData, $converter->convertDataToArray(array($objectA)));
    }

    public function mappingDataProvider()
    {
        return array(
            array(
                array('object' => new Varien_Object(array('keyA' => 'valueA'))),
                array('object' => array('keyA' => 'valueA'))
            ),
            array(
                array('objectA' => new Varien_Object(array('keyA' => 'valueA')),
                      'objectB' => new Varien_Object(array(
                                                          'keyB' => new Varien_Object(array(
                                                                                           'keyC'     => 'valueC',
                                                                                           'password' => 'qa123123'))
                                                     ))),
                array('objectA' => array('keyA' => 'valueA'),
                      'objectB' => array(
                          'keyB' => array(
                              'keyC'     => 'valueC',
                              'password' => 'qa123123'))) // We no longer redact as part of conversion
            ),
            array(
                array(),
                array()
            ),
            array(
                array(555888, 'string'    => "Some text",
                      'not_varien_object' => new Varien_Convert_ObjectTest_SimpleClass(
                          'private', 'protected', 'public'
                      )),
                array(555888, 'string' => "Some text", 'not_varien_object' => array(
                    ' Varien_Convert_ObjectTest_SimpleClass _privateField' => 'private',
                    ' * _protectedField'         => 'protected',
                    'publicField'                => 'public',
                )),
            ),
            array(
                array(
                    array(
                        'some_object' => new Varien_Object(
                            array(
                                 'keyA' => array(
                                     new Varien_Object(
                                         array(
                                              'sub_key' => 'sub_value'
                                         )
                                     )
                                 )
                            )
                        )
                    )
                ),
                array(
                    array(
                        'some_object' => array(
                            'keyA' => array(
                                array(
                                    'sub_key' => 'sub_value'
                                )
                            )
                        )
                    )
                ),
            ),
        );
    }
}

class Varien_Convert_ObjectTest_SimpleClass
{
    public $publicField;

    protected $_protectedField;

    private $_privateField;

    public function __construct($private, $protected, $public)
    {
        $this->_privateField = $private;
        $this->_protectedField = $protected;
        $this->publicField = $public;
    }

    public function getPrivateField()
    {
        return $this->_privateField;
    }
}