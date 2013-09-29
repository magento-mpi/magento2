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
        $filesystem = $this->getMockBuilder('Magento\Filesystem')->disableOriginalConstructor()->getMock();
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $catalogProduct = $this->getMock('Magento\Catalog\Helper\Product', array(), array(), '', false);
        $catalogData = $this->getMock('Magento\Catalog\Helper\Data', array(), array(), '', false);
        $coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $fileStorageDb = $this->getMock('Magento\Core\Helper\File\Storage\Database', array(), array(), '', false);
        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);
        $bundleModelSelection = $this->getMock('Magento\Bundle\Model\SelectionFactory', array(), array(), '', false);
        $bundleFactory = $this->getMock('Magento\Bundle\Model\Resource\BundleFactory', array(), array(), '', false);
        $bundleCollection = $this->getMock(
            'Magento\Bundle\Model\Resource\Selection\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $config = $this->getMock('Magento\Catalog\Model\Config', array(), array(), '', false);
        $bundleSelection = $this->getMock('Magento\Bundle\Model\Resource\Selection', array(), array(), '', false);
        $bundleOption = $this->getMock('Magento\Bundle\Model\OptionFactory', array(), array(), '', false);
        $storeManager = $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false);
        $this->_model = new \Magento\Bundle\Model\Product\Type(
            $bundleModelSelection,
            $bundleFactory,
            $bundleCollection,
            $config,
            $bundleSelection,
            $bundleOption,
            $storeManager,
            $eventManager,
            $catalogProduct,
            $catalogData,
            $coreData,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger
        );
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
