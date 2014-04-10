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
        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $downloadableFile = $this->getMockBuilder(
            'Magento\Downloadable\Helper\File'
        )->disableOriginalConstructor()->getMock();
        $coreData = $this->getMockBuilder('Magento\Core\Helper\Data')->disableOriginalConstructor()->getMock();
        $fileStorageDb = $this->getMockBuilder(
            'Magento\Core\Helper\File\Storage\Database'
        )->disableOriginalConstructor()->getMock();
        $filesystem = $this->getMockBuilder('Magento\Framework\App\Filesystem')->disableOriginalConstructor()->getMock();
        $coreRegistry = $this->getMock('Magento\Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
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
                'linkFactory' => $linkFactory
            )
        );
    }

    public function testHasWeightFalse()
    {
        $this->assertFalse($this->_model->hasWeight(), 'This product has weight, but it should not');
    }
}
