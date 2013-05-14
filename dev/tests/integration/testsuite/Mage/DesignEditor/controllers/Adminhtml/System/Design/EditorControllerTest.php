<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Adminhtml_System_Design_EditorControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_dataHelper;

    protected function setUp()
    {
        parent::setUp();
        $this->_dataHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
    }

    public function testIndexAction()
    {
        $this->dispatch('backend/admin/system_design_editor/index');
        $content = $this->getResponse()->getBody();

        $this->assertContains('Choose a theme to start with', $content);
        $this->assertContains('<div class="infinite_scroll">', $content);
        $this->assertContains("jQuery('.infinite_scroll').infinite_scroll", $content);
    }

    public function testLaunchActionSingleStoreWrongThemeId()
    {
        $wrongThemeId = 999;
        $this->getRequest()->setParam('theme_id', $wrongThemeId);
        $this->dispatch('backend/admin/system_design_editor/launch');
        $this->assertSessionMessages($this->equalTo(
            array('Theme "' . $wrongThemeId . '" was not found.')),
            Mage_Core_Model_Message::ERROR
        );
        $expected = 'http://localhost/index.php/backend/admin/system_design_editor/index/';
        $this->assertRedirect($this->stringStartsWith($expected));
    }

    /**
     * @param array $source
     * @param array $result
     * @param bool $isXml
     *
     * @dataProvider getLayoutUpdateActionDataProvider
     */
    public function testGetLayoutUpdateAction(array $source, array $result, $isXml = false)
    {
        $this->getRequest()->setPost($source);
        $this->dispatch('backend/admin/system_design_editor/getLayoutUpdate');
        $response = $this->_dataHelper->jsonDecode($this->getResponse()->getBody());

        // convert to XML string to the same format as in $result
        if ($isXml) {
            foreach ($response as $code => $data) {
                foreach ($data as $key => $value) {
                    $xml = new Varien_Simplexml_Element($value);
                    $response[$code][$key] = $xml->asNiceXml();
                }
            }
        }
        $this->assertEquals($result, $response);
    }

    /**
     * @return array
     */
    public static function getLayoutUpdateActionDataProvider()
    {
        $correctXml = new Varien_Simplexml_Element('<?xml version="1.0" encoding="UTF-8"?><layout/>');
        $correctXml = $correctXml->asNiceXml();

        return array(
            'no history data' => array(
                '$source' => array(),
                '$result' => array(
                    Mage_Core_Model_Message::ERROR => array('Invalid post data')
                ),
            ),
            'correct data' => array(
                '$source' => array('historyData' => array(
                    array (
                        'handle'                => 'current_handle',
                        'type'                  => 'layout',
                        'element_name'          => 'tags_popular',
                        'action_name'           => 'move',
                        'destination_container' => 'content',
                        'destination_order'     => '1',
                        'origin_container'      => 'left',
                        'origin_order'          => '1',
                    ),
                    array (
                        'handle'                => 'current_handle',
                        'type'                  => 'layout',
                        'element_name'          => 'tags_popular',
                        'action_name'           => 'move',
                        'destination_container' => 'left',
                        'destination_order'     => '1',
                        'origin_container'      => 'content',
                        'origin_order'          => '1',
                    ),
                )),
                '$result' => array(
                    Mage_Core_Model_Message::SUCCESS => array($correctXml)
                ),
                '$isXml' => true,
            ),
        );
    }
}
