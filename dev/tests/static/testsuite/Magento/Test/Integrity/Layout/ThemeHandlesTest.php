<?php
/**
 * Test declarations of handles in theme layout updates
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Layout;

class ThemeHandlesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array|bool
     */
    protected $_codeFrontendHandles = false;

    /**
     * Check that all handles declared in a theme layout are declared in code
     *
     * @param string $handleName
     * @dataProvider designHandlesDataProvider
     */

    public function testIsDesignHandleDeclaredInCode($handleName)
    {
        $this->assertArrayHasKey(
            $handleName,
            $this->_getCodeFrontendHandles(),
            "Handle '{$handleName}' is not declared in any module.'"
        );
    }

    /**
     * @return array
     */
    public function designHandlesDataProvider()
    {
        $files = \Magento\TestFramework\Utility\Files::init()->getLayoutFiles(array(
            'include_code' => false,
            'area' => 'frontend'
        ));

        $handles = array();
        foreach (array_keys($files) as $path) {
            $xml = simplexml_load_file($path);
            $handleNodes = $xml->xpath('/layout/*') ?: array();
            foreach ($handleNodes as $handleNode) {
                $handles[] = $handleNode->getName();
            }
        }

        $result = array();
        foreach (array_unique($handles) as $handleName) {
            $result[] = array($handleName);
        }
        return $result;
    }

    /**
     * Returns information about handles that are declared in code for frontend
     *
     * @return array
     */
    protected function _getCodeFrontendHandles()
    {
        if ($this->_codeFrontendHandles) {
            return $this->_codeFrontendHandles;
        }

        $files = \Magento\TestFramework\Utility\Files::init()->getLayoutFiles(array(
            'include_design' => false,
            'area' => 'frontend'
        ));
        foreach (array_keys($files) as $path) {
            $xml = simplexml_load_file($path);
            $handleNodes = $xml->xpath('/layout/*') ?: array();
            foreach ($handleNodes as $handleNode) {
                $isLabel = $handleNode->xpath('label');
                if (isset($handles[$handleNode->getName()]['label_count'])) {
                    $handles[$handleNode->getName()]['label_count'] += (int)$isLabel;
                } else {
                    $handles[$handleNode->getName()]['label_count'] = (int)$isLabel;
                }
            }
        }

        $this->_codeFrontendHandles = $handles;
        return $this->_codeFrontendHandles;
    }
}
