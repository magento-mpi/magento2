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

class Magento_Downloadable_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Downloadable\Model\Product\Type
     */
    protected $_model;

    protected function setUp()
    {
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $downloadableFile = $this->getMockBuilder('Magento\Downloadable\Helper\File')
            ->disableOriginalConstructor()->getMock();
        $coreData = $this->getMockBuilder('Magento\Core\Helper\Data')->disableOriginalConstructor()->getMock();
        $fileStorageDb = $this->getMockBuilder('Magento\Core\Helper\File\Storage\Database')
            ->disableOriginalConstructor()->getMock();
        $filesystem = $this->getMockBuilder('Magento\Filesystem')->disableOriginalConstructor()->getMock();

        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $this->_model = new \Magento\Downloadable\Model\Product\Type(
            $eventManager, $downloadableFile, $coreData, $fileStorageDb, $filesystem, $coreRegistry
        );
    }

    public function testHasWeightFalse()
    {
        $this->assertFalse($this->_model->hasWeight(), 'This product has weight, but it should not');
    }
}
