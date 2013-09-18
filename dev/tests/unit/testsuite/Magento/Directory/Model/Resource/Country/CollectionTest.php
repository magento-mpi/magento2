<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Directory_Model_Resource_Country_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Directory\Model\Resource\Country\Collection
     */
    protected $_model;

    protected function setUp()
    {
        $connection = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false);
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $connection->expects($this->once())
            ->method('select')
            ->will($this->returnValue($select));

        $resource = $this->getMockForAbstractClass('Magento\Core\Model\Resource\Db\AbstractDb', array(), '',
            false, true,
            true, array('getReadConnection', 'getMainTable', 'getTable'));
        $resource->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($connection));
        $resource->expects($this->any())
            ->method('getTable')
            ->will($this->returnArgument(0));

        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $helperMock = $this->getMock('Magento\Core\Helper\String', array(), array(), '', false);
        $localeMock = $this->getMock('Magento\Core\Model\LocaleInterface');
        $localeMock->expects($this->any())->method('getCountryTranslation')->will($this->returnArgument(0));

        $fetchStrategy = $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface');
        $storeConfigMock = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $this->_model = $this->getMock(
            'Magento_Directory_Model_Resource_Country_Collection',
            array('_toOptionArray'),
            array($eventManager, $helperMock, $localeMock, $fetchStrategy, $storeConfigMock, $resource),
            '',
            true
        );
    }

    /**
     * @dataProvider toOptionArrayDataProvider
     * @param array $optionsArray
     * @param string|boolean $emptyLabel
     * @param string|array $foregroundCountries
     * @param array $expectedResults
     */
    public function testToOptionArray($optionsArray, $emptyLabel, $foregroundCountries, $expectedResults)
    {
        $this->_model->expects($this->any())
            ->method('_toOptionArray')
            ->will($this->returnValue($optionsArray));

        $this->_model->setForegroundCountries($foregroundCountries);
        $result = $this->_model->toOptionArray($emptyLabel);
        $this->assertEquals(count($optionsArray) + (int)!empty($emptyLabel), count($result));
        foreach ($expectedResults as $index => $expectedResult) {
            $this->assertEquals($expectedResult, $result[$index]['label']);
        }
    }

    /**
     * @return array
     */
    public function toOptionArrayDataProvider()
    {
        $optionsArray = array(
            array('title' => 'AD', 'value' => 'AD', 'label' => ''),
            array('title' => 'US', 'value' => 'US', 'label' => ''),
            array('title' => 'ES', 'value' => 'ES', 'label' => ''),
            array('title' => 'BZ', 'value' => 'BZ', 'label' => ''),
        );
        return array(
            array($optionsArray, false, array(), array('AD', 'US', 'ES', 'BZ')),
            array($optionsArray, false, 'US', array('US', 'AD', 'ES', 'BZ')),
            array($optionsArray, false, array('US', 'BZ'), array('US', 'BZ', 'AD', 'ES')),
            array($optionsArray, ' ', 'US', array(' ', 'US', 'AD', 'ES', 'BZ')),
        );
    }
}
