<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Theme_Preview_DefaultTest extends Mage_DesignEditor_Model_Theme_Preview
{
    /**
     * @param array $themeData
     *
     * @dataProvider themeDataFromConfiguration
     */
    public function testGetUrl($themeData)
    {
        $theme = $this->_getTheme($themeData);

        /** @var $preview Mage_DesignEditor_Model_Theme_Preview_Default */
        $preview = Mage::getModel('Mage_DesignEditor_Model_Theme_Preview_Default');
        $previewUrl = $preview->setTheme($theme)->getPreviewUrl();

        $this->assertStringMatchesFormat('http://localhost/index.php/?SID=%s&___store=%s', $previewUrl);
    }
}
