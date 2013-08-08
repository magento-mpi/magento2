<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Saas_Saas_Controller_Mage_Backend_Adminhtml_System_Config_SaveTest extends Mage_Backend_Utility_Controller
{
    /**
     * @param array $restrictedOptions
     * @param bool $expectedIsSaved
     * @magentoDbIsolation enabled
     * @dataProvider saveOptionDataProvider
     */
    public function testSaveOption(array $restrictedOptions, $expectedIsSaved)
    {
        // Add our custom config
        $config = new Saas_Saas_Model_DisabledConfiguration_Config($restrictedOptions);
        $this->_objectManager->configure(array(
            'Saas_Saas_Model_DisabledConfiguration_Observer' => array(
                'parameters' => array('config' => $config),
            )
        ));

        // Section and value to be tested on
        $section = 'system';
        $group = 'smtp';
        $field = 'host';
        $testValue = "a_value_to_be_tested_by_saas_save";

        // Assert preconditions - the value must not exist before
        $entry = $this->_getConfigDataByValue($testValue);
        $this->assertEmpty($entry, 'The value should not exist before testing');

        // Dispatch POST
        $post = array(
            'groups' => array(
                $group => array(
                    'fields' => array(
                        $field => array('value' => $testValue),
                    ),
                ),
            ),
        );
        $this->getRequest()->setPost($post);
        $this->dispatch("backend/admin/system_config_save/index/section/{$section}");

        // Assert that the value was saved/not saved
        $entry = $this->_getConfigDataByValue($testValue);
        if ($expectedIsSaved) {
            $this->assertNotEmpty($entry, 'The value was not saved');
        } else {
            $this->assertEmpty($entry, 'The value was saved');
        }
    }

    /**
     * Return config data model with the $value, or null if not found.
     *
     * @param string $value
     * @return Magento_Core_Model_Config_Data|null
     */
    protected function _getConfigDataByValue($value)
    {
        $configData = $this->_objectManager->create('Magento_Core_Model_Config_Data');
        $configData->load($value, 'value');
        return $configData->getId() ? $configData : null;
    }

    /**
     * @return array
     */
    public static function saveOptionDataProvider()
    {
        return array(
            'permitted field' => array(
                array('customer'),
                true,
            ),
            'disabled field' => array(
                array('system/smtp/host'),
                false,
            ),
            'disabled group' => array(
                array('system/smtp'),
                false,
            ),
            'disabled section' => array(
                array('system'),
                false,
            ),
        );
    }
}
