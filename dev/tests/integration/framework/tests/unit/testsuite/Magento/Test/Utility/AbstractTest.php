<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ruslan.voitenko
 * Date: 2/24/12
 * Time: 5:57 PM
 * To change this template use File | Settings | File Templates.
 */
class Magento_Test_Utility_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testAbstractUtility()
    {
        $mockedAbstractUtility = $this->getMock('Magento_Test_Utility_Abstract', null, array($this));

        $this->assertTrue(($mockedAbstractUtility->getTestCase() instanceof PHPUnit_Framework_TestCase));
    }
}
