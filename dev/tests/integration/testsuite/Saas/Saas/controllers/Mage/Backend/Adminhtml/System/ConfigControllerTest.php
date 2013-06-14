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
class Saas_Saas_Mage_Backend_Adminhtml_System_ConfigControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @param $action
     * @param array $restrictedOptions
     * @param array $absentSelectors
     * @dataProvider removeRestrictedOptionsDataProvider
     */
    public function testRemoveRestrictedOptions($action, array $restrictedOptions, array $absentSelectors)
    {
        $this->markTestIncomplete('Incorrect parameters format. MAGETWO-10538');

        $this->_injectCustomConverter($restrictedOptions);

        $this->dispatch($action);
        $body = $this->getResponse()->getBody();
        foreach ($absentSelectors as $selector) {
            $this->assertSelectEquals($selector['selector'], $selector['content'], 0, $body, $selector['message']);
        }
    }

    /**
     * Injects custom converter filter, configured with a fixture list of disabled controls
     *
     * @param array $restrictedOptions
     */
    protected function _injectCustomConverter(array $restrictedOptions)
    {
        $config = new Saas_Saas_Model_DisabledConfiguration_Config($restrictedOptions);
        $this->_objectManager->configure(array(
            'Saas_Saas_Model_DisabledConfiguration_Structure_Converter_Filter' => array(
                'parameters' => array('disabledConfig' => $config),
            )
        ));

        // Disable cache of already composed config, so it will be processed again, using the saas converter
        /** @var $cacheTypes Mage_Core_Model_Cache_Types */
        $cacheTypes = $this->_objectManager->get('Mage_Core_Model_Cache_Types');
        $cacheTypes->setEnabled(Mage_Core_Model_Cache_Type_Config::TYPE_IDENTIFIER, false);
    }

    /**
     * @return array
     */
    public function removeRestrictedOptionsDataProvider()
    {
        return array(
            'restricted fields' => array(
                '/backend/admin/system_config/edit/section/web',
                array('web/url/use_store', 'web/unsecure/base_url'),
                array(
                    array('selector' => 'groups[url][fields][use_store][value]', 'content' => false,
                          'message' => '"Add Store Code to Urls" field must not be present on the page'),
                    array('selector' => 'groups[unsecure][fields][base_url][value]', 'content' => false,
                          'message' => '"Base URLs" -> "Base URL" field must not be present on the page'),
                )
            ),
            'restricted groups' => array(
                '/backend/admin/system_config/edit/section/web',
                array('web/url', 'web/seo'),
                array(
                    array('selector' => '#web_url-head', 'content' => 'regexp:/Url Options/',
                          'message' => '"Url Options" group must not be present on the page'),
                    array('selector' => 'groups[url][fields][use_store][value]', 'content' => true,
                          'message' => '"Add Store Code to Urls" field must not be present on the page'),
                    array('selector' => 'groups[url][fields][redirect_to_base][value]', 'content' => true,
                          'message' => '"Auto-redirect to Base URL" field must not be present on the page'),
                    array('selector' => '#web_url-head', 'content' => 'regexp:/Search Engines Optimization/',
                          'message' => '"Search Engines Optimization" group must not be present on the page'),
                    array('selector' => 'groups[seo][fields][use_rewrites][value]', 'content' => true,
                          'message' => '"Use Web Server Rewrites" field must not be present on the page'),
                ),
            ),
            'restricted sections' => array(
                '/backend/admin/system_config/edit/section/web',
                array('design', 'dev'),
                array(
                    array('selector' => '#system_config_tabs .item-nav span', 'content' => 'regexp:/Design/',
                          'message' => '"Design" section must not be present in the menu'),
                    array('selector' => '#system_config_tabs .item-nav span', 'content' => 'regexp:/Developer/',
                          'message' => '"Developer" section must not be present in the menu'),
                ),
            ),
        );
    }

    /**
     * Check that restricted section is inaccessible
     */
    public function testRemoveRestrictedOptionsSection()
    {
        $this->_injectCustomConverter(array('web'));

        $this->dispatch('/backend/admin/system_config/edit/section/web');
        $body = $this->getResponse()->getBody();
        $this->assertContains('Access denied', $body);
    }
}
