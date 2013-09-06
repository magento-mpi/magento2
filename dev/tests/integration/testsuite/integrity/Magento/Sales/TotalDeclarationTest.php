<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Magento_Sales_TotalDeclarationTest extends PHPUnit_Framework_TestCase
{
    public function testTotalDeclarations()
    {
        $config = array();
        foreach (Mage::getConfig()->getNode('global/sales/quote/totals')->asCanonicalArray() as $key => $row) {
            $config[$key] = array(
                'before' => empty($row['before']) ? array() : explode(',', $row['before']),
                'after'  => empty($row['after']) ? array() : explode(',', $row['after']),
            );
        }
        Magento_Sales_Model_Config_Ordered::validateCollectorDeclarations($config);
    }
}
