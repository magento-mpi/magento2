<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Saas_Model_DisabledConfiguration_Structure_Converter_FilterTest extends PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        $baseConfig = array(
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section_allowed' => array(
                            'children' => array(
                                'group_allowed' => array(
                                    'children' => array('field_allowed' => array()),
                                ),
                                'group_restricted' => array(
                                    'children' => array(
                                        'field_allowed' => array(),
                                        'field_restricted' => array()
                                    ),
                                ),
                            ),
                        ),
                        'section_restricted' => array(
                            'children' => array(
                                'group' => array(
                                    'children' => array('field' => null,),
                                ),
                            ),
                        ),
                    ),
                ),
                'non_system' => null,
            ),
        );

        $map = $this->getMockForAbstractClass('Mage_Backend_Model_Config_Structure_MapperAbstract');
        $map->expects($this->any())
            ->method('map')
            ->will($this->returnValue($baseConfig));

        $mapperFactory = $this->getMock('Mage_Backend_Model_Config_Structure_Mapper_Factory', array(), array(), '',
            false);
        $mapperFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($map));

        $restrictedOptions = array(
            'section_restricted',
            'section_allowed/group_restricted',
            'section_allowed/group_allowed/field_restricted'
        );
        $config = new Saas_Saas_Model_DisabledConfiguration_Config($restrictedOptions);
        $model = new Saas_Saas_Model_DisabledConfiguration_Structure_Converter_Filter($mapperFactory, $config);
        $domDocument = dom_import_simplexml(simplexml_load_string('<config><system></system></config>'));
        $result = $model->convert($domDocument);
        $expected = array(
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section_allowed' => array(
                            'children' => array(
                                'group_allowed' => array(
                                    'children' => array('field_allowed' => array()),
                                ),
                            ),
                        ),
                    ),
                ),
                'non_system' => null,
            ),
        );
        $this->assertEquals($expected, $result);
    }
}
