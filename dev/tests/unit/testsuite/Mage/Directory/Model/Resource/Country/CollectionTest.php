<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Directory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Directory_Model_Resource_Country_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Stub_UnitTest_Mage_Directory_Model_Resource_Country_Collection
     */
    protected $_model;
    
    /**
     * @var string
     */
    public static $fixturePath;

    protected function setUp()
    {
        self::$fixturePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $connection = $this->getMock('Varien_Db_Adapter_Pdo_Mysql',
            array(), array(), '', false
        );
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $connection->expects($this->once())
            ->method('select')
            ->will($this->returnValue($select));
        $connection->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));

        $resource = $this->getMockForAbstractClass('Mage_Core_Model_Resource_Db_Abstract', array(), '', false, true,
            true, array('getReadConnection', 'getMainTable', 'getTable'));
        $resource->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($connection));
        $resource->expects($this->any())
            ->method('getTable')
            ->will($this->returnArgument(0));
        
        $helperMock = $this->getMock('Mage_Core_Helper_String', array(), array(), '', false);
        $localeMock = $this->getMock('Mage_Core_Model_LocaleInterface');
        $localeMock->expects($this->any())->method('getCountryTranslation')
            ->will($this->returnArgument(0));
        
        $this->_model = new Stub_UnitTest_Mage_Directory_Model_Resource_Country_Collection($helperMock, $localeMock, $resource);
    }
    
    public function testToOptionArray()
    {
        $optionsArray = include(Mage_Directory_Model_Resource_Country_CollectionTest::$fixturePath . 'options_array.php');
        
        $result = $this->_model->toOptionArray();
        $this->assertEquals(count($optionsArray) + 1, count($result));
        $this->assertEquals($optionsArray[0]['title'], $result[1]['value']);
        
        $result = $this->_model->toOptionArray(false);
        $this->assertEquals(count($optionsArray), count($result));
        $this->assertEquals($optionsArray[0]['title'], $result[0]['value']);
        
        $this->_model->setForegroundCountries('US');
        $result = $this->_model->toOptionArray(false);
        $this->assertEquals(count($optionsArray), count($result));
        $this->assertEquals($result[0]['value'], 'US');        
        
        $this->_model->setForegroundCountries(array('US', 'BZ'));
        $result = $this->_model->toOptionArray(false);
        $this->assertEquals(count($optionsArray), count($result));
        $this->assertEquals($result[0]['value'], 'US');
        $this->assertEquals($result[1]['value'], 'BZ');
    }
}

class Stub_UnitTest_Mage_Directory_Model_Resource_Country_Collection
    extends Mage_Directory_Model_Resource_Country_Collection
{
    /**
     * Stub parent constructor
     * 
     * @param Mage_Core_Helper_String $stringHelper
     * @param Mage_Core_Model_LocaleInterface $locale
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(Mage_Core_Helper_String $stringHelper, Mage_Core_Model_LocaleInterface $locale, $resource = null)
    {
        parent::__construct($stringHelper, $locale, $resource);
    }
    
    /**
     * Return items array
     * array(
     *      $index => array(
     *          'value' => mixed
     *          'label' => mixed
     *      )
     * )
     * @return  array
     */
    protected function _toOptionArray()
    {
        return include(Mage_Directory_Model_Resource_Country_CollectionTest::$fixturePath . 'options_array.php');
    }
}
