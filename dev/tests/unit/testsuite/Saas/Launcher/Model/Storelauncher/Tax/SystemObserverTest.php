<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_Storelauncher_Tax_SystemObserverTest extends PHPUnit_Framework_TestCase
{
    public function testHandleTaxRuleSave()
    {
        $model = $this->_getModelForHandleTaxRuleSaveTest();
        $observer = new Magento_Event_Observer();
        $model->handleTaxRuleSave($observer);
    }

    protected function _getModelForHandleTaxRuleSaveTest()
    {
        // Mock tax tile
        $tile = $this->getMock(
            'Saas_Launcher_Model_Tile',
            array('setState', 'save'),
            array(),
            '',
            false
        );

        // Tax tile must change its state to complete when any tax rule has been saved
        $tile->expects($this->once())
            ->method('setState')
            ->with($this->equalTo(Saas_Launcher_Model_Tile::STATE_COMPLETE))
            ->will($this->returnValue($tile));
        $tile->expects($this->once())
            ->method('save');

        // Return mocked tile when factory method is called
        $tileFactory = $this->getMock(
            'Saas_Launcher_Model_TileFactory',
            array('create'),
            array(),
            '',
            false
        );
        $tileFactory->expects($this->any())
            ->method('create')
            ->with(
                $this->equalTo('tax'),
                $this->equalTo(array())
            )
            ->will($this->returnValue($tile));

        return new Saas_Launcher_Model_Storelauncher_Tax_SystemObserver($tileFactory);
    }
}
