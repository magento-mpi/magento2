<?php
/**
 * Cms TestSuite
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class CmsTestSuite extends Mage_TestSuite
{
    /**
     * Check is enable module and add tests to suite
     *
     * @return Mage_TestSuite
     */
    public static function suite()
    {
        $suite = new Mage_TestSuite();
        if (!self::_isModuleEnable('Mage_Cms')) {
            self::_findTests($suite, dirname(__FILE__), true);
        }
        else {
            echo "-- Module Mage_Cms is disabled\n";
        }

        $reg = Mage::registry();
        foreach ($reg as $k => $v) {
            echo $k . ' = ';
            if (is_object($v)) {
                echo get_class($v);
            }
            else {
                echo $v;
            }
            echo "\n";
        }

        echo "\n\n";

        return $suite;
    }
}
