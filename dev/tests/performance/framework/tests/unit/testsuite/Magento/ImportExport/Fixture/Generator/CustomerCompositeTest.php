<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ImportExport_Fixture_Generator_CustomerCompositeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $templateRecord
     * @param array $expectedColumns
     * @dataProvider replacementsDataProvider
     */
    public function testReplacements($templateRecord, $expectedColumns)
    {
        $model = new Magento_ImportExport_Fixture_Generator_CustomerComposite($templateRecord, 1);
        foreach ($model as $row) {
            break;
        }

        foreach ($expectedColumns as $column => $regPattern) {
            $this->assertRegExp($regPattern, $row[$column]);
        }
    }

    public function replacementsDataProvider()
    {
        return array(
            'usual_replacements' => array(
                'templateRecord' => array(
                    'any_column'            => 'value %s there %s',
                    'created_at'            => '%x',
                    'firstname'             => '%x',
                    'lastname'              => '%x',
                    'gender'                => '%x',
                    '_address_firstname'    => '%x',
                    '_address_lastname'     => '%x',
                    'nonspecial_column'     => '%x'
                ),
                'expectedColumns' => array(
                    'any_column'            => '/^value 1 there 1$/',
                    'created_at'            => '/^\\d{2}-\\d{2}-\\d{4} \\d{2}:\\d{2}$/',
                    'firstname'             => '/^[A-Z][a-z]+$/',
                    'lastname'              => '/^[A-Z][a-z]+$/',
                    'gender'                => '/(Male|Female)/',
                    '_address_firstname'    => '/^[A-Z][a-z]+$/',
                    '_address_lastname'     => '/^[A-Z][a-z]+$/',
                    'nonspecial_column'     => '/^%x$/',
                ),
            ),
            'replacements_in_special_column' => array(
                'templateRecord' => array(
                    'created_at'            => 'value %s there %s',
                    'firstname'             => 'Innokentiy',
                ),
                'expectedColumns' => array(
                    'created_at'            => '/^value 1 there 1$/',
                    'firstname'             => '/^Innokentiy$/',
                ),
            ),
        );
    }
}
