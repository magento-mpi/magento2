<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Product;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    protected $_model;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectHelper->getObject(
            'Magento\Bundle\Model\Product\Type',
            array(
                'productFactory' => $this->getMock('Magento\Catalog\Model\ProductFactory'),
                'bundleModelSelection' => $this->getMock('Magento\Bundle\Model\SelectionFactory'),
                'bundleFactory' => $this->getMock('Magento\Bundle\Model\Resource\BundleFactory'),
                'bundleCollection' => $this->getMock('Magento\Bundle\Model\Resource\Selection\CollectionFactory'),
                'bundleOption' => $this->getMock('Magento\Bundle\Model\OptionFactory')
            )
        );
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }

    public function testGetIdentities()
    {
        $identities = array('id1', 'id2');
        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $optionMock = $this->getMock(
            '\Magento\Bundle\Model\Option',
            array('getSelections', '__wakeup'),
            array(),
            '',
            false
        );
        $optionCollectionMock = $this->getMock(
            'Magento\Bundle\Model\Resource\Option\Collection',
            array(),
            array(),
            '',
            false
        );
        $cacheKey = '_cache_instance_options_collection';
        $productMock->expects($this->once())
            ->method('getIdentities')
            ->will($this->returnValue($identities));
        $productMock->expects($this->once())
            ->method('hasData')
            ->with($cacheKey)
            ->will($this->returnValue(true));
        $productMock->expects($this->once())
            ->method('getData')
            ->with($cacheKey)
            ->will($this->returnValue($optionCollectionMock));
        $optionCollectionMock
            ->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue(array($optionMock)));
        $optionMock
            ->expects($this->exactly(2))
            ->method('getSelections')
            ->will($this->returnValue(array($productMock)));
        $this->assertEquals($identities, $this->_model->getIdentities($productMock));
    }
}
