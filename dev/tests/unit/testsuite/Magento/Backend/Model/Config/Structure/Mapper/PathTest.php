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
namespace Magento\Backend\Model\Config\Structure\Mapper;

class PathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Mapper\Path
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Backend\Model\Config\Structure\Mapper\Path();
    }

    public function testMap()
    {
        $data = array(
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section_1' => array(
                            'id' => 'section_1',
                            'children' => array(
                                'group_1' => array(
                                    'id' => 'group_1',
                                    'children' => array(
                                        'field_1' => array('id' => 'field_1'),
                                        'group_1.1' => array(
                                            'id' => 'group_1.1',
                                            'children' => array('field_1.2' => array('id' => 'field_1.2'))
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        $expected = array(
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section_1' => array(
                            'id' => 'section_1',
                            'children' => array(
                                'group_1' => array(
                                    'id' => 'group_1',
                                    'children' => array(
                                        'field_1' => array('id' => 'field_1', 'path' => 'section_1/group_1'),
                                        'group_1.1' => array(
                                            'id' => 'group_1.1',
                                            'children' => array(
                                                'field_1.2' => array(
                                                    'id' => 'field_1.2',
                                                    'path' => 'section_1/group_1/group_1.1'
                                                )
                                            ),
                                            'path' => 'section_1/group_1'
                                        )
                                    ),
                                    'path' => 'section_1'
                                )
                            )
                        )
                    )
                )
            )
        );

        $actual = $this->_model->map($data);
        $this->assertEquals($expected, $actual);
    }
}
