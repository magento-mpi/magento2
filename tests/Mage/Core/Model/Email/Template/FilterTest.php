<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Tests
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Template filter model test case
 *
 */
class Mage_Core_Model_Email_Template_FilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Email_Template_Filter
     */
    private $_filterInstance = null;

    /**
     * Get filter instance for multiple usage
     *
     * @return Mage_Core_Model_Email_Template_Filter
     */
    private function _getFilterInstance()
    {
        if (null === $this->_filterInstance) {
            $this->_filterInstance = Mage::getModel('core/email_template_filter');
        }
        return $this->_filterInstance;
    }

    public function setUp()
    {
        Mage::app();
    }

    /**
     * @dataProvider modifierEscapeDataProvider
     */
    public function testModifierEscape($input, $expected, $type = 'html')
    {
        $this->assertSame($expected, $this->_getFilterInstance()->modifierEscape($input, $type));
    }

    /**
     * @dataProvider modifierAmplificationDataProvider
     */
    public function testModifierAmplification($variables, $template, $expected)
    {
        $filter = clone $this->_getFilterInstance();
        $this->assertSame($expected, $filter->setVariables($variables)->filter($template));
    }

    /**
     * Data provider for escape modifier test
     *
     * @return array
     */
    public function modifierEscapeDataProvider()
    {
        return array(
            array('&', '&amp;'),
            array('>', '&gt;', 'html'),
            array('"', '&quot;', 'htmlentities'),
            array("A 'quote' is <b>bold</b>", 'A &#039;quote&#039; is &lt;b&gt;bold&lt;/b&gt;', 'htmlentities'),
            array('foo @+%/', 'foo%20%40%2B%25%2F', 'url'),
        );
    }

    /**
     * Data provider for modifiers amplification test
     *
     * @return array
     */
    public function modifierAmplificationDataProvider()
    {
        return array(
            array(
                array('name' => 'Bob & Marley'),
                '{{var name|escape}}',
                htmlspecialchars('Bob & Marley', ENT_QUOTES),
            ),
            array(
                array('name' => ' ', 'message' => "a\nwrapped message"),
                '{{var name|escape|nl2br}} {{var message|nl2br}}',
                '  ' . nl2br("a\nwrapped message")
            ),
            array(
                array('message' => "wrapped<br/>\nmsg"),
                '{{var message|nl2br|escape:html|escape:url}} | {{var message|nl2br|escape:html:url:html|escape:url::::::}}',
                rawurlencode(htmlspecialchars(nl2br("wrapped<br/>\nmsg"), ENT_QUOTES))
                . ' | ' . rawurlencode(htmlspecialchars(nl2br("wrapped<br/>\nmsg"), ENT_QUOTES))
            ),
        );
    }
}
