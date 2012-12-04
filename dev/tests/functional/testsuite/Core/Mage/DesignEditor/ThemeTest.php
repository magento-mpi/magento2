<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme list
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_DesignEditor_ThemeTest extends Mage_Selenium_TestCase
{
    /**
     * @test
     */
    public function openAvailableThemePage()
    {
        $this->loginAdminUser();
        $this->navigate('design_editor_selector');
        $this->waitForAjax();
        $this->isElementPresent('infinite_scroll');

        $xpath = $this->_getControlXpath('pageelement', 'theme_list_elements');
        $defaultElementsCount = $this->getXpathCount($xpath);

        /** 4 - default page size for theme collection */
        $this->assertEquals(4, $defaultElementsCount);
        $lastElementId = $this->getAttribute($xpath . "[$defaultElementsCount]/@id");

        $destinationOffsetTop = $this->getEval("this.browserbot.findElement('id=" . $lastElementId . "').offsetTop");
        $this->getEval("this.browserbot.findElement('class=infinite_scroll').scrollTop = " . $destinationOffsetTop);

        $this->waitForAjax();
        /** 8 - the number of items on 2 pages */
        $this->assertEquals(8, $this->getXpathCount($xpath));
    }
}
