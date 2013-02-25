<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Design_SaveHandlerTest
    extends Mage_Launcher_Model_Tile_ConfigBased_SaveHandler_TestCaseAbstract
{
    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Backend_Model_Config $backendConfigModel
     * @return Mage_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
     */
    public function getSaveHandlerInstance(
        Mage_Core_Model_Config $config,
        Mage_Backend_Model_Config $backendConfigModel
    ) {
        return new Mage_Launcher_Model_Storelauncher_Design_SaveHandler(
            $config,
            $backendConfigModel
        );
    }

    /**
     * This data provider emulates invalid input for prepareData method
     *
     * @return array
     */
    public function prepareDataInvalidInputDataProvider()
    {
        $data0 = array();
        $data0['groups']['design']['theme']['fields']['theme_id']['value'] = '';

        $data1 = array();

        return array(
            array($data0),
            array($data1),
        );
    }

    /**
     * This data provider emulates valid input for prepareData method
     *
     * @return array
     */
    public function prepareDataValidInputDataProvider()
    {
        return array(
            array(
                $this->_getTestData(),
                $this->_getExpectedData(),
                array('design')
            ),
        );
    }

    /**
     * Get array of test data, emulating request data
     *
     * @return array
     */
    protected function _getTestData()
    {
        $result = array(
            'groups' => array(
                'design' => array(
                    'theme' => array(
                        'fields' => array(
                            'theme_id' => array('value' => '1'),
                        ),
                    ),

                ),
            ),
            'tileCode' => 'design',
        );

        return $result;
    }

    /**
     * Get Expected data
     *
     * @return array
     */
    protected function _getExpectedData()
    {
        $result = array(
            'design' => array(
                'theme' => array(
                    'fields' => array(
                        'theme_id' => array('value' => '1'),
                    ),
                ),
            ),
        );
        return $result;
    }
}
