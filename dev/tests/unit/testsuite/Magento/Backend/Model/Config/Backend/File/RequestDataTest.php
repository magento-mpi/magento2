<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend\File;

class RequestDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Backend\File\RequestData
     */
    protected $_model;

    protected function setUp()
    {
        $_FILES = array(
            'groups' => array(
                'name' => array(
                    'group_1' => array('fields' => array('field_1' => array('value' => 'file_name_1'))),
                    'group_2' => array(
                        'groups' => array(
                            'group_2_1' => array('fields' => array('field_2' => array('value' => 'file_name_2')))
                        )
                    )
                ),
                'tmp_name' => array(
                    'group_1' => array('fields' => array('field_1' => array('value' => 'file_tmp_name_1'))),
                    'group_2' => array(
                        'groups' => array(
                            'group_2_1' => array('fields' => array('field_2' => array('value' => 'file_tmp_name_2')))
                        )
                    )
                )
            )
        );

        $this->_model = new \Magento\Backend\Model\Config\Backend\File\RequestData();
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetNameRetrievesFileName()
    {
        $this->assertEquals('file_name_1', $this->_model->getName('section_1/group_1/field_1'));
        $this->assertEquals('file_name_2', $this->_model->getName('section_1/group_2/group_2_1/field_2'));
    }

    public function testGetTmpNameRetrievesFileName()
    {
        $this->assertEquals('file_tmp_name_1', $this->_model->getTmpName('section_1/group_1/field_1'));
        $this->assertEquals('file_tmp_name_2', $this->_model->getTmpName('section_1/group_2/group_2_1/field_2'));
    }

    public function testGetNameReturnsNullIfInvalidPathIsProvided()
    {
        $this->assertNull($this->_model->getName('section_1/group_2/field_1'));
        $this->assertNull($this->_model->getName('section_1/group_3/field_1'));
        $this->assertNull($this->_model->getName('section_1/group_1/field_2'));
        $this->assertNull($this->_model->getName('section_1/group_1'));
    }
}
