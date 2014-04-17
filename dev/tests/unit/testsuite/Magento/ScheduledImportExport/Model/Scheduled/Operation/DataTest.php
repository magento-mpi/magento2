<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model\Scheduled\Operation;

/**
 * Class DataTest
 *
 * @package Magento\ScheduledImportExport\Model\Scheduled\Operation
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data
     */
    protected $model;

    protected function setUp()
    {
        $importConfigMock = $this->getMockBuilder('Magento\ImportExport\Model\Import\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $exportConfigMock = $this->getMockBuilder('Magento\ImportExport\Model\Export\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = new \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data(
            $importConfigMock,
            $exportConfigMock
        );
    }

    /**
     * Test getServerTypesOptionArray()
     */
    public function testGetServerTypesOptionArray()
    {
        $expected = [
            Data::FILE_STORAGE => 'Local Server',
            Data::FTP_STORAGE => 'Remote FTP'
        ];
        $result = $this->model->getServerTypesOptionArray();
        $this->assertEquals($expected, $result);
    }
}
