<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Helper_TranslateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Template helper mock
     *
     * @var Mage_Core_Helper_Translate
     */
    protected $_helper;

    protected function setUp()
    {
        parent::setUp();
        $this->_helper = new Mage_Core_Helper_Translate();
    }

    public function testComposeLocaleHierarchy()
    {
        $localeConfig = array(
            'en_US' => 'en_UK',
            'en_UK' => 'pt_BR',
        );
        $localeHierarchy = array(
            'en_US' => array('pt_BR', 'en_UK'),
            'en_UK' => array('pt_BR'),
        );
        $this->assertEquals($localeHierarchy, $this->_helper->composeLocaleHierarchy($localeConfig));
    }
}
