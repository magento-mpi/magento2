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

class Mage_Launcher_Model_Storelauncher_Design_SaveHandlerTest extends PHPUnit_Framework_TestCase
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
        $configStub = $this->getMock(
            'Mage_Backend_Model_Config',
            array('setSection', 'setGroups', 'save'),
            array(),
            '',
            false
        );

        $setSectionWith = $this->logicalOr(
            $this->equalTo('general'),
            $this->equalTo('design')
        );

        $setGroupsWith = $this->logicalOr(
            $this->equalTo($expectedData['general']),
            $this->equalTo($expectedData['design'])
        );

        $configStub->expects($this->exactly($timesToCall))
            ->method('setSection')
            ->with($setSectionWith)
            ->will($this->returnValue($configStub));

        $configStub->expects($this->exactly($timesToCall))
            ->method('setGroups')
            ->with($setGroupsWith)
            ->will($this->returnValue($configStub));

        $configStub->expects($this->exactly($timesToCall))
            ->method('save');

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Design_SaveHandler(
            $configStub
        );
        $saveHandler->save($data);
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
                2
            ),
        );
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
        $configStub = $this->getMock(
            'Mage_Backend_Model_Config',
            array(),
            array(),
            '',
            false
        );

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Design_SaveHandler(
            $configStub
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
                'general' => array(
                    'store_information' => array(
                        'fields' => array(
                            'name' => array('value' => 'Store Name 1'),
                        ),
                    ),
                ),
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
            'general' => array(
                'store_information' => array(
                    'fields' => array(
                        'name' => array('value' => 'Store Name 1'),
                    ),
                ),
            ),
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
