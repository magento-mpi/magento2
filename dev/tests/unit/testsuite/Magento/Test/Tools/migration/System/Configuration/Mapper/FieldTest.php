<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../../../')
    . '/tools/migration/System/Configuration/Mapper/Abstract.php';

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../../../../')
    . '/tools/migration/System/Configuration/Mapper/Field.php';

/**
 * Test case for Tools_Migration_System_Configuration_Mapper_Field
 */
class Magento_Test_Tools_Migration_System_Configuration_Mapper_FieldTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tools_Migration_System_Configuration_Mapper_Field
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Magento_Tools_Migration_System_Configuration_Mapper_Field();
    }

    protected function tearDown()
    {
        $this->_object = null;
    }

    public function testTransform()
    {
        $config = array(
            'field_1' => array(
                'comment' => array('#cdata-section' => 'comment_test'),
                'tooltip' => array('#text' => 'tooltip_test'),
                'frontend_class' => array('#text' => 'frontend_class_test'),
                'validate' => array('#text' => 'validate_test'),
                'can_be_empty' => array('#text' => 'can_be_empty_test'),
                'if_module_enabled' => array('#text' => 'if_module_enabled_test'),
                'frontend_model' => array('#text' => 'frontend_model_test'),
                'backend_model' => array('#text' => 'backend_model_test'),
                'source_model' => array('#text' => 'source_model_test'),
                'config_path' => array('#text' => 'config_path_test'),
                'base_url' => array('#text' => 'base_url_test'),
                'upload_dir' => array('#text' => 'upload_dir_test'),
                'button_url' => array('#text' => 'button_url_test'),
                'button_label' => array('#text' => 'button_label_test'),
                'depends' => array(
                    'module1' => array('#text' => 'yes')
                ),
                'more_url' => array('#text' => 'more_url_test'),
                'demo_url' => array('#text' => 'demo_url_test'),
                'undefined' => array('#text' => 'undefined_test', '@attributes' => array('some' => 'attribute')),
                'node' => array(
                    'label' => array(
                        'nodeLabel' => array('#text' => 'nodeValue')
                    ),
                ),
            ),
        );

        $expected = array(
            array(
                'nodeName' => 'field',
                '@attributes' => array('id' => 'field_1'),
                'parameters' => array(
                    array('name' => 'comment', '#cdata-section' => 'comment_test'),
                    array('name' => 'tooltip', '#text' => 'tooltip_test'),
                    array('name' => 'frontend_class', '#text' => 'frontend_class_test'),
                    array('name' => 'validate', '#text' => 'validate_test'),
                    array('name' => 'can_be_empty', '#text' => 'can_be_empty_test'),
                    array('name' => 'if_module_enabled', '#text' => 'if_module_enabled_test'),
                    array('name' => 'frontend_model', '#text' => 'frontend_model_test'),
                    array('name' => 'backend_model', '#text' => 'backend_model_test'),
                    array('name' => 'source_model', '#text' => 'source_model_test'),
                    array('name' => 'config_path', '#text' => 'config_path_test'),
                    array('name' => 'base_url', '#text' => 'base_url_test'),
                    array('name' => 'upload_dir','#text' => 'upload_dir_test'),
                    array('name' => 'button_url', '#text' => 'button_url_test',),
                    array('name' => 'button_label', '#text' => 'button_label_test'),
                    array(
                        'name' => 'depends',
                        'subConfig' => array(
                            array(
                                'nodeName' => 'field',
                                '@attributes' => array('id' => 'module1'),
                                '#text' => 'yes'
                            )
                        )
                    ),
                    array('name' => 'more_url', '#text' => 'more_url_test'),
                    array('name' => 'demo_url', '#text' => 'demo_url_test'),
                    array(
                        '@attributes' => array(
                            'type' => 'undefined',
                            'some' => 'attribute',
                        ),
                        'name' => 'attribute',
                        '#text' => 'undefined_test',
                    ),
                    array(
                        '@attributes' => array(
                            'type' => 'node'
                        ),
                        'name' => 'attribute',
                        'subConfig' => array(
                            array(
                                'nodeName' => 'label',
                                'subConfig' => array(
                                    array(
                                        'nodeName' => 'nodeLabel',
                                        '#text' => 'nodeValue',
                                    )
                                )
                            )
                        )
                    )
                ),
            ),
        );

        $actual = $this->_object->transform($config);
        $this->assertEquals($expected, $actual);
    }
}
