<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Helper;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ConditionsTest
 */
class ConditionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditions;

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->conditions = $objectManagerHelper->getObject('Magento\Widget\Helper\Conditions');
    }

    public function testEncodeDecode()
    {
        $value = array(
            '1' => [
                "type" => "Magento\\CatalogWidget\\Model\\Rule\\Condition\\Combine",
                "aggregator" => "all",
                "value" => "1",
                "new_child" => ""
            ],
            '1--1' => [
                "type" => "Magento\\CatalogWidget\\Model\\Rule\\Condition\\Product",
                "attribute" => "attribute_set_id",
                "value" => "4",
                "operator" => "=="
            ],
            '1--2' => [
                "type" => "Magento\\CatalogWidget\\Model\\Rule\\Condition\\Product",
                "attribute" => "category_ids",
                "value" => "2",
                "operator" => "=="
            ],
        );
        $encoded = $this->conditions->encode($value);
        $this->assertEquals($value, $this->conditions->decode($encoded));
    }
}
