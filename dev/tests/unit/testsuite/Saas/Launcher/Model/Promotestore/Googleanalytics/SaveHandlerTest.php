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

class Saas_Launcher_Model_Promotestore_Googleanalytics_SaveHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Save function test
     *
     * @dataProvider generateSaveData
     * @param array $data Request data
     * @param array $expectedData
     * @param int $timesToCall
     */
    public function testSave($data, $expectedData, $timesToCall)
    {
        $backendConfigModel = $this->getMock(
            'Magento_Backend_Model_Config',
            array('setSection', 'setGroups', 'save'),
            array(),
            '',
            false
        );

        $backendConfigModel->expects($this->exactly($timesToCall))
            ->method('setSection')
            ->with($this->equalTo('google'))
            ->will($this->returnValue($backendConfigModel));

        $backendConfigModel->expects($this->exactly($timesToCall))
            ->method('setGroups')
            ->with($this->equalTo($expectedData['google']))
            ->will($this->returnValue($backendConfigModel));

        $backendConfigModel->expects($this->exactly($timesToCall))
            ->method('save');

        // Mock core configuration model
        $config = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);

        $saveHandler = new Saas_Launcher_Model_Promotestore_Googleanalytics_SaveHandler(
            $config,
            $backendConfigModel

        );
        $saveHandler->save($data);
    }

    /**
     * Prepare Address Data for system configuration test
     *
     * @dataProvider generatePrepareData
     * @param array $data
     * @param array $expectedData
     */
    public function testPrepareData($data, $expectedData)
    {
        $backendConfigModel = $this->getMock(
            'Magento_Backend_Model_Config',
            array(),
            array(),
            '',
            false
        );
        // Mock core configuration model
        $config = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);

        $saveHandler = new Saas_Launcher_Model_Promotestore_Googleanalytics_SaveHandler(
            $config,
            $backendConfigModel
        );

        $result = $saveHandler->prepareData($data);
        $this->assertEquals($expectedData, $result);
    }

    /**
     * Data provider for testPrepareData method
     *
     * @return array
     */
    public function generatePrepareData()
    {
        return array(
            array(
                $this->_getTestData(),
                $this->_getExpectedData()
            ),
            array(
                $this->_getTestData(true),
                $this->_getExpectedData(true)
            )
        );
    }

    /**
     * Data provider for testSave methods
     *
     * @return array
     */
    public function generateSaveData()
    {
        return array(
            array(
                $this->_getTestData(),
                $this->_getExpectedData(),
                1
            ),
            array(
                $this->_getTestData(true),
                $this->_getExpectedData(true),
                1
            )
        );
    }

    /**
     * Get array of test data, emulating request data
     *
     * @param bool $empty
     * @return array
     */
    protected function _getTestData($empty = false)
    {
        $account = ($empty) ? '' : 'accountId';
        $result = array(
            'groups' => array(
                'google' => array(
                    'analytics' => array(
                        'fields' => array(
                            'account' => array('value' => $account),
                        ),
                    ),
                )
            )
        );
        return $result;
    }

    /**
     * Get Expected data
     *
     * @param bool $empty
     * @return array
     */
    protected function _getExpectedData($empty = false)
    {
        $result = array(
            'google' => array(
                'analytics' => array(
                    'fields' => array(
                        'account' => array('value' => 'accountId'),
                        'active' =>  array('value' => 1)
                    ),
                ),
            ),
        );
        if ($empty) {
            $result['google']['analytics']['fields']['account']['value'] = '';
            unset($result['google']['analytics']['fields']['active']);
        }
        return $result;
    }
}
