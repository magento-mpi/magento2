<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\Product;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Downloadable\Model\Product\Type
     */
    protected $_model;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', array(), array(), '', false);
        $downloadableFile = $this->getMockBuilder(
            'Magento\Downloadable\Helper\File'
        )->disableOriginalConstructor()->getMock();
        $coreData = $this->getMockBuilder('Magento\Core\Helper\Data')->disableOriginalConstructor()->getMock();
        $fileStorageDb = $this->getMockBuilder(
            'Magento\Core\Helper\File\Storage\Database'
        )->disableOriginalConstructor()->getMock();
        $filesystem = $this->getMockBuilder('Magento\Framework\App\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $coreRegistry = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento\Framework\Logger', array(), array(), '', false);
        $productFactoryMock = $this->getMock('Magento\Catalog\Model\ProductFactory', array(), array(), '', false);
        $sampleResFactory = $this->getMock(
            'Magento\Downloadable\Model\Resource\SampleFactory',
            array(),
            array(),
            '',
            false
        );
        $linkResource = $this->getMock('Magento\Downloadable\Model\Resource\Link', array(), array(), '', false);
        $linksFactory = $this->getMock(
            'Magento\Downloadable\Model\Resource\Link\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $samplesFactory = $this->getMock(
            'Magento\Downloadable\Model\Resource\Sample\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $sampleFactory = $this->getMock('Magento\Downloadable\Model\SampleFactory', array(), array(), '', false);
        $linkFactory = $this->getMock('Magento\Downloadable\Model\LinkFactory', array(), array(), '', false);

        $entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', array(), array(), '', false);
        $resourceProductMock = $this->getMock('Magento\Catalog\Model\Resource\Product', array('getEntityType'), array(), '', false);
        $resourceProductMock->expects($this->any())->method('getEntityType')->will($this->returnValue($entityTypeMock));

        $productMock = $this->getMock('Magento\Catalog\Model\Product', array('getResource', 'canAffectOptions', 'getLinksPurchasedSeparately', 'setTypeHasRequiredOptions', 'setRequiredOptions', 'getDownloadableData', 'setTypeHasOptions', 'setLinksExist', '__wakeup'), array(), '', false);
        $productMock->expects($this->any())->method('getResource')->will($this->returnValue($resourceProductMock));
        $productMock->expects($this->any())->method('setTypeHasRequiredOptions')->with($this->equalTo(true))->will($this->returnSelf());
        $productMock->expects($this->any())->method('setRequiredOptions')->with($this->equalTo(true))->will($this->returnSelf());
        $productMock->expects($this->any())->method('getDownloadableData')->will($this->returnValue(array()));
        $productMock->expects($this->any())->method('setTypeHasOptions')->with($this->equalTo(false));
        $productMock->expects($this->any())->method('setLinksExist')->with($this->equalTo(false));
        $productMock->expects($this->any())->method('canAffectOptions')->with($this->equalTo(true));
        $productMock->expects($this->any())->method('getLinksPurchasedSeparately')->will($this->returnValue(true));
        $productMock->expects($this->any())->method('getLinksPurchasedSeparately')->will($this->returnValue(true));
        $this->_productMock = $productMock;

        $eavConfigMock = $this->getMock('\Magento\Eav\Model\Config', array('getEntityAttributeCodes'), array(), '', false);
        $eavConfigMock->expects($this->any())
            ->method('getEntityAttributeCodes')
            ->with($this->equalTo($entityTypeMock), $this->equalTo($productMock))
            ->will($this->returnValue(array()));

        $this->_model = $objectHelper->getObject(
            'Magento\Downloadable\Model\Product\Type',
            array(
                'eventManager' => $eventManager,
                'downloadableFile' => $downloadableFile,
                'coreData' => $coreData,
                'fileStorageDb' => $fileStorageDb,
                'filesystem' => $filesystem,
                'coreRegistry' => $coreRegistry,
                'logger' => $logger,
                'productFactory' => $productFactoryMock,
                'sampleResFactory' => $sampleResFactory,
                'linkResource' => $linkResource,
                'linksFactory' => $linksFactory,
                'samplesFactory' => $samplesFactory,
                'sampleFactory' => $sampleFactory,
                'linkFactory' => $linkFactory,
                'eavConfig' => $eavConfigMock
            )
        );
    }

    public function testHasWeightFalse()
    {
        $this->assertFalse($this->_model->hasWeight(), 'This product has weight, but it should not');
    }

    public function testBeforeSave()
    {
        $this->_model->beforeSave($this->_productMock);
    }
}
