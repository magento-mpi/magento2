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

namespace Magento\Catalog\Model\Product\Attribute\Backend;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Backend\Media
     */
    protected $_model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectHelper;

    protected function setUp()
    {
        $this->_objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);

        $fileStorageDb = $this->getMock('Magento\Core\Helper\File\Storage\Database', array(), array(), '', false);
        $coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $resource = $this->getMock('Magento\Catalog\Model\Resource\Product\Attribute\Backend\Media',
            array('getMainTable'), array(), '', false);
        $resource->expects($this->any())
            ->method('getMainTable')
            ->will($this->returnValue('table'));

        $mediaConfig = $this->getMock('Magento\Catalog\Model\Product\Media\Config', array(), array(), '', false);
        $dirs = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false);
        $filesystem = $this->getMockBuilder('Magento\Filesystem')->disableOriginalConstructor()->getMock();
        $this->_model = $this->_objectHelper->getObject('Magento\Catalog\Model\Product\Attribute\Backend\Media', array(
            'eventManager' => $eventManager,
            'fileStorageDb' => $fileStorageDb,
            'coreData' => $coreData,
            'mediaConfig' => $mediaConfig,
            'dirs' => $dirs,
            'filesystem' => $filesystem,
            'resourceProductAttribute' => $resource,
        ));
    }

    public function testGetAffectedFields()
    {
        $valueId = 2345;
        $attributeId = 345345;

        $attribute = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\AbstractAttribute',
            array('getBackendTable', 'isStatic', 'getAttributeId', 'getName'),
            array(),
            '',
            false
        );
        $attribute->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('image'));

        $attribute->expects($this->any())
            ->method('getAttributeId')
            ->will($this->returnValue($attributeId));

        $attribute->expects($this->any())
            ->method('isStatic')
            ->will($this->returnValue(false));

        $attribute->expects($this->any())
            ->method('getBackendTable')
            ->will($this->returnValue('table'));


        $this->_model->setAttribute($attribute);

        $object = new \Magento\Object();
        $object->setImage(array(
            'images' => array(array(
                'value_id' => $valueId
            ))
        ));
        $object->setId(555);

        $this->assertEquals(
            array(
                'table' => array(array(
                    'value_id' => $valueId,
                    'attribute_id' => $attributeId,
                    'entity_id' => $object->getId(),
                ))
            ),
            $this->_model->getAffectedFields($object)
        );
    }
}
