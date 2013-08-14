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

class Saas_Launcher_Model_Storelauncher_Tax_SaveHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Save function test
     *
     * @dataProvider generateSaveData
     * @param array $data Request data
     * @param int $timesToCall
     */
    public function testSave($data, $timesToCall)
    {
        $taxRule = $this->getMock(
            'Magento_Tax_Model_Calculation_Rule',
            array('save'),
            array(),
            '',
            false
        );

        $taxRule->expects($this->exactly($timesToCall))
            ->method('save');

        $saveHandler = new Saas_Launcher_Model_Storelauncher_Tax_SaveHandler(
            $taxRule
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
                $this->_getTestData(true),
                1
            ),
            array(
                $this->_getTestData(false),
                0
            ),
        );
    }

    /**
     * Get array of test data, emulating request data
     *
     * @param bool $useTax
     * @return array
     */
    protected function _getTestData($useTax = false)
    {
        return array(
            'isAjax' => 'true',
            'code' => 'testrule',
            'tax_customer_class' => array(
                0 => '1',
            ),
            'tax_product_class' => array(
                0 => '1',
            ),
            'priority' => '0',
            'position' => '0',
            'use_tax' => $useTax ? '1' : '0',
            'tileCode' => 'tax',
        );

    }
}
