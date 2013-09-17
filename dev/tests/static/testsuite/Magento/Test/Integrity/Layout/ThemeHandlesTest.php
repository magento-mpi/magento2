<?php
/**
 * Test declarations of handles in theme layout updates
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Layout_ThemeHandlesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array|null
     */
    protected $_baseFrontendHandles = null;

    /**
     * Check that all handles declared in a theme layout are declared in base layouts
     *
     * @param string $handleName
     * @dataProvider designHandlesDataProvider
     */
    public function testIsDesignHandleDeclaredInCode($handleName)
    {
        $this->assertContains(
            $handleName,
            $this->_getBaseFrontendHandles(),
            "Handle '{$handleName}' is not declared in any module.'"
        );
    }

    /**
     * @return array
     */
    public function designHandlesDataProvider()
    {
        $files = Magento_TestFramework_Utility_Files::init()->getLayoutFiles(array(
            'include_code' => false,
            'area' => 'frontend'
        ), false);
        $handles = $this->_extractLayoutHandles($files);
        $result = array();
        foreach ($handles as $handleName) {
            $result[$handleName] = array($handleName);
        }
        return $result;
    }

    /**
     * Return layout handles that are declared in the base layouts for frontend
     *
     * @return array
     */
    protected function _getBaseFrontendHandles()
    {
        if ($this->_baseFrontendHandles === null) {
            $files = Magento_TestFramework_Utility_Files::init()->getLayoutFiles(array(
                'include_design' => false,
                'area' => 'frontend'
            ), false);
            $this->_baseFrontendHandles = $this->_extractLayoutHandles($files);
        }
        return $this->_baseFrontendHandles;
    }

    /**
     * Retrieve the list of unique layout handle names from the layout files
     *
     * @param array $files
     * @return array
     */
    protected function _extractLayoutHandles(array $files)
    {
        $result = array();
        foreach ($files as $filename) {
            $handleName = basename($filename, '.xml');
            $result[] = $handleName;
        }
        $result = array_unique($result);
        return $result;
    }
}
