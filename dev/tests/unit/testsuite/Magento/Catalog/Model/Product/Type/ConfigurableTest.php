<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Type;

/**
 * Class \Magento\Catalog\Model\Product\Type\ConfigurableTest
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Type\Configurable
     */
    protected $_model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectHelper;

    protected function setUp()
    {
        $this->_objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $coreDataMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $fileStorageDbMock = $this->getMock('Magento\Core\Helper\File\Storage\Database', array(), array(), '', false);
        $filesystem = $this->getMockBuilder('Magento\App\Filesystem')->disableOriginalConstructor()->getMock();
        $coreRegistry = $this->getMock('Magento\Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $productFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', array(), array(), '', false);
        $confFactoryMock = $this->getMock('Magento\Catalog\Model\Resource\Product\Type\ConfigurableFactory',
            array(), array(), '', false);
        $entityFactoryMock = $this->getMock('Magento\Eav\Model\EntityFactory', array(), array(), '', false);
        $setFactoryMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\SetFactory', array(), array(), '', false);
        $attributeFactoryMock = $this->getMock('Magento\Catalog\Model\Resource\Eav\AttributeFactory', array(),
            array(), '', false);
        $confAttrFactoryMock = $this->getMock('Magento\Catalog\Model\Product\Type\Configurable\AttributeFactory',
            array(), array(), '', false);
        $productColFactory = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Type\Configurable\Product\CollectionFactory',
            array(), array(), '', false
        );
        $attrColFactory = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Type\Configurable\Attribute\CollectionFactory',
            array(), array(), '', false
        );
        $this->_model = $this->_objectHelper->getObject('Magento\Catalog\Model\Product\Type\Configurable', array(
            'productFactory' => $productFactoryMock,
            'typeConfigurableFactory' => $confFactoryMock,
            'entityFactory' => $entityFactoryMock,
            'attributeSetFactory' => $setFactoryMock,
            'eavAttributeFactory' => $attributeFactoryMock,
            'configurableAttributeFactory' => $confAttrFactoryMock,
            'productCollectionFactory' => $productColFactory,
            'attributeCollectionFactory' => $attrColFactory,
            'eventManager' => $eventManager,
            'coreData' => $coreDataMock,
            'fileStorageDb' => $fileStorageDbMock,
            'filesystem' => $filesystem,
            'coreRegistry' => $coreRegistry,
            'logger' => $logger
        ));
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
