<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Mage_Backend_Adminhtml_System_Config_SaveControllerTest extends Mage_Backend_Utility_Controller
{
    public static function setUpBeforeClass()
    {
        include_once __DIR__ . '/_files/ModulesReader.php';
    }

    public function setUp()
    {
        $this->_getBootstrap()->reinitialize(array(Mage::PARAM_BAN_CACHE => 1)); // To embed fixture system.xml

        parent::setUp();

        $disabledConfiguration = array(
            'disabled_section',
            'permitted_section/disabled_group',
            'permitted_section/permitted_group/disabled_field'
        );
        $this->_objectManager->configure(
            array(
                'Saas_Saas_Model_DisabledConfiguration_Config' => array(
                    'parameters' => array('plainList' => $disabledConfiguration),
                ),
                'preferences' => array(
                    'Mage_Core_Model_Config_Modules_Reader'
                        => 'Saas_Mage_Backend_Adminhtml_System_Config_ModulesReader', // Will embed fixture system.xml
                ),
            )
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @dataProvider saveOptionDataProvider
     */
    public function testSaveOption($section, $group, $field, $expectedIsSaveSuccessful)
    {
        // Assert preconditions - no values exist before
        $entry = $this->_getConfigDataByPath("{$section}/{$group}/{$field}");
        $this->assertEmpty($entry, 'The field should not exist before testing');

        // Dispatch POST
        $post = array(
            'groups' => array(
                $group => array(
                    'fields' => array(
                        $field => array('value' => 'any_value'),
                    ),
                ),
            ),
        );
        $this->getRequest()->setPost($post);
        $this->dispatch("backend/admin/system_config_save/index/section/{$section}");

        // Assert that the value was saved/not saved
        $entry = $this->_getConfigDataByPath("{$section}/{$group}/{$field}");
        if ($expectedIsSaveSuccessful) {
            $this->assertNotEmpty($entry, 'The value was not saved');
        } else {
            $this->assertEmpty($entry, 'The value was saved');
        }
    }

    /**
     * @return array
     */
    public static function saveOptionDataProvider()
    {
        return array(
            'permitted field' => array(
                'permitted_section',
                'permitted_group',
                'permitted_field',
                true,
            ),
            'disabled field' => array(
                'permitted_section',
                'permitted_group',
                'disabled_field',
                false,
            ),
            'disabled group' => array(
                'permitted_section',
                'disabled_group',
                'permitted_field',
                false,
            ),
            'disabled section' => array(
                'disabled section',
                'permitted_group',
                'permitted_field',
                false,
            ),
        );
    }

    /**
     * Return config data model with the $value, or null if not found.
     *
     * @param string $value
     * @return Mage_Core_Model_Config_Data|null
     */
    protected function _getConfigDataByPath($value)
    {
        $configData = $this->_objectManager->create('Mage_Core_Model_Config_Data');
        $configData->load($value, 'path');
        return $configData->getId() ? $configData : null;
    }
}
