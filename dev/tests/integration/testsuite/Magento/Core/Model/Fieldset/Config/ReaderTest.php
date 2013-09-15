<?php
/**
 * \Magento\Core\Model\Fieldset\Config\Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
 */
class Magento_Core_Model_Fieldset_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Fieldset\Config\Reader
     */
    protected $_model;

    public function setUp()
    {
        /** @var \Magento\Core\Model\Dir $dirs */
        $dirs = Mage::getObjectManager()->create(
            'Magento\Core\Model\Dir', array(
                'baseDir' => BP,
                'dirs' => array(
                    \Magento\Core\Model\Dir::MODULES => __DIR__ . '/_files',
                    \Magento\Core\Model\Dir::CONFIG => __DIR__ . '/_files'
                )
            )
        );

        /** @var Magento_Core_Model_Module_Declaration_FileResolver $modulesDeclarations */
        $modulesDeclarations = Mage::getObjectManager()->create(
            'Magento_Core_Model_Module_Declaration_FileResolver', array(
                'applicationDirs' => $dirs,
            )
        );


        /** @var Magento_Core_Model_Module_Declaration_Reader_Filesystem $filesystemReader */
        $filesystemReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Module_Declaration_Reader_Filesystem', array(
                'fileResolver' => $modulesDeclarations,
            )
        );

        /** @var \Magento\Core\Model\ModuleList $modulesList */
        $modulesList = Mage::getObjectManager()->create(
            'Magento\Core\Model\ModuleList', array(
                'reader' => $filesystemReader,
            )
        );

        /** @var \Magento\Core\Model\Config\Modules\Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create(
            'Magento\Core\Model\Config\Modules\Reader', array(
                'dirs' => $dirs,
                'moduleList' => $modulesList
            )
        );

        /** @var \Magento\Core\Model\Config\FileResolver $fileResolver */
        $fileResolver = Mage::getObjectManager()->create(
            'Magento\Core\Model\Config\FileResolver', array(
                'moduleReader' => $moduleReader,
            )
        );

        $this->_model = Mage::getObjectManager()->create(
            'Magento\Core\Model\Fieldset\Config\Reader', array(
                'fileResolver' => $fileResolver,
            )
        );
    }

    public function testRead()
    {
        $result = $this->_model->read('global');
        $expected = include '_files/expectedArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/_files/partialFieldsetFirst.xml',
            __DIR__ . '/_files/partialFieldsetSecond.xml'
        );
        $fileResolverMock = $this->getMockBuilder('Magento\Config\FileResolverInterface')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileResolverMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('fieldset.xml'), $this->equalTo('global'))
            ->will($this->returnValue($fileList));

        /** @var \Magento\Core\Model\Fieldset\Config\Reader $model */
        $model = Mage::getObjectManager()->create(
            'Magento\Core\Model\Fieldset\Config\Reader', array(
                'fileResolver' => $fileResolverMock,
            )
        );
        $expected = array(
            'global' => array(
                'sales_convert_quote_item' => array(
                    'event_id' => array(
                        'to_order_item' => "*",
                    ),
                    'event_name' => array(
                        'to_order_item' => "*"
                    ),
                    'event_description' => array(
                        'to_order_item' => "complexDesciption"
                    )
                )
            )
        );
        $this->assertEquals($expected, $model->read('global'));
    }
}
