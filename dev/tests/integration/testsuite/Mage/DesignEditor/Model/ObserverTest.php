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
     * @param bool $isActive
     * @param string $skin
     * @param string|null $expectedSkin
     *
     * @dataProvider applyCustomSkinDesignDataProvider
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function testApplyCustomSkinDesignNotActive($isActive, $skin, $expectedSkin)
    {
        $session = $this->getMock('Mage_DesignEditor_Model_Session', array('isDesignEditorActive'));
        $session->expects($this->any())
            ->method('isDesignEditorActive')
            ->will($this->returnValue($isActive));
        $session->setSkin($skin);

        $observer = $this->getMock('Mage_DesignEditor_Model_Observer', array('_getSession'));
        $observer->expects($this->any())
            ->method('_getSession')
            ->will($this->returnValue($session));

        $oldSkin = Mage::getDesign()->getDesignTheme();

        $e = null;
        $expectedSkin = $expectedSkin ?: $oldSkin;
        try {
            $observer->applyCustomSkin(new Varien_Event_Observer());
            $this->assertEquals($expectedSkin, Mage::getDesign()->getDesignTheme());
        } catch (Exception $e) {
        }
        Mage::getDesign()->setDesignTheme($oldSkin);
        if ($e) {
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function applyCustomSkinDesignDataProvider()
    {
        return array(
            array(false, 'default/default/blank', null),
            array(true, '', null),
            array(true, 'default/default/blank', 'default/default/blank')
        );
    }
}
