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

/**
 * Test for skin changing observer
 *
 * @group module:Mage_DesignEditor
 */
class Mage_DesignEditor_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Model_Observer
     */
    protected $_observer;

    protected function setUp()
    {
        $this->_observer = new Mage_DesignEditor_Model_Observer;
    }

    /**
     * @param string $skin
     * @param string|null $expectedSkin
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @dataProvider applyCustomSkinDesignDataProvider
     */
    public function testApplyCustomSkinDesign($skin, $expectedSkin)
    {
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $session->setSkin($skin);
        $oldSkin = Mage::getDesign()->getDesignTheme();
        $this->assertNotEquals($skin, $oldSkin);

        $expectedSkin = $expectedSkin ?: $oldSkin;
        $this->_observer->applyCustomSkin(new Varien_Event_Observer());
        $this->assertEquals($expectedSkin, Mage::getDesign()->getDesignTheme());
    }

    /**
     * @return array
     */
    public function applyCustomSkinDesignDataProvider()
    {
        return array(
            array('', null),
            array('default/default/blank', 'default/default/blank')
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testApplyCustomSkinDesignNotActive()
    {
        $newSkin = 'default/default/blank';
        $oldSkin = Mage::getDesign()->getDesignTheme();
        $this->assertNotEquals($newSkin, $oldSkin);

        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $session->setSkin($newSkin);

        $this->_observer->applyCustomSkin(new Varien_Event_Observer());
        $this->assertEquals($oldSkin, Mage::getDesign()->getDesignTheme());
    }

    /**
     * @param array $params
     * @param string $expectedHtml
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @dataProvider wrapHtmlWithBlockInfoDataProvider
     */
    public function testWrapHtmlWithBlockInfo($params, $expectedHtml)
    {
        $observerData = $this->_buildObserverData($params);
        $this->_observer->wrapHtmlWithBlockInfo($observerData);
        $this->assertEquals($expectedHtml, $observerData->getTransport()->getHtml());
    }

    public function wrapHtmlWithBlockInfoDataProvider()
    {
        return array(
            'normal_block' => array(
                'params' => array(
                    'name' => 'block.name',
                    'html' => '<div>Any text</div>'
                ),
                'expectedHtml' => '<br class="vde_marker" block_name="block.name" marker_type="start"/>'
                    . '<div>Any text</div>'
                    . '<br class="vde_marker" marker_type="end"/>'
            ),
            'no_wrap_body' => array(
                'params' => array(
                    'name' => 'block.name',
                    'html' => '<title>Title</title><body>Body</body>'
                ),
                'expectedHtml' => '<title>Title</title><body>Body</body>'
            ),
            'no_wrap_unknown_content' => array(
                'params' => array(
                    'name' => 'block.name',
                    'html' => 'Some content'
                ),
                'expectedHtml' => 'Some content'
            ),
            'no_wrap_vde_blocks' => array(
                'params' => array(
                    'name' => 'block.name',
                    'html' => '<div>Any text</div>',
                    'class' => 'Mage_DesignEditor_Block_That_Belongs_To_That_Module'
                ),
                'expectedHtml' => '<div>Any text</div>'
            )
        );
    }

    protected function _buildObserverData($params)
    {
        if (isset($params['class'])) {
            $block = $this->getMock('Mage_Core_Block_Template', array(), array(), $params['class']);
        } else {
            $block = new Mage_Core_Block_Template;
        }
        $block->setNameInLayout($params['name']);

        $transport = new Varien_Object();
        $transport->setHtml($params['html']);

        $result = new Varien_Event_Observer;
        $result->setTransport($transport);
        $result->setBlock($block);
        return $result;
    }
}
