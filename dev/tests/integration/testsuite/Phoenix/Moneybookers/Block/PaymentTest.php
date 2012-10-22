<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Phoenix_Moneybookers
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Phoenix_Moneybookers_Block_PaymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_localeCode;

    protected function setUp()
    {
        $this->_localeCode = Mage::app()->getLocale()->getLocale();
    }

    protected function tearDown()
    {
        Mage::app()->getLocale()->setLocale($this->_localeCode);
    }

    /**
     * @dataProvider getMoneybookersLogoSrcDataProvider
     */
    public function testGetMoneybookersLogoSrc($localeCode, $expectedFile)
    {
        Mage::app()->getLocale()->setLocale($localeCode);
        /** @var $blockFactory Mage_Core_Model_BlockFactory */
        $blockFactory = Mage::getObjectManager()->get('Mage_Core_Model_BlockFactory');
        $block = $blockFactory->createBlock('Phoenix_Moneybookers_Block_Payment');
        $this->assertStringEndsWith($expectedFile, $block->getMoneybookersLogoSrc());
    }

    /**
     * @return array
     */
    public function getMoneybookersLogoSrcDataProvider()
    {
        return array(
            array('en_US', 'banner_120_int.gif'),
            array('de_DE', 'banner_120_de.png'),
            array('br_PT', 'banner_120_int.gif'),
        );
    }
}
