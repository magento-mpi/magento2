<?php
/**
 * Test constructions of layout files
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * Pattern for attribute elements, compatible with HTML ID
     */
    const HTML_ID_PATTERN = '/^[a-z][a-z\-\_\d]*$/';

    /**
     * @var array|bool
     */
    protected $_codeFrontendHandles = false;

    /**
     * @var array|bool
     */
    protected $_pageHandles = false;

    /**
     * Check count of layout handle labels that described in modules for frontend area
     *
     * @param string $handleName
     * @param int $labelCount
     *
     * @dataProvider handleLabelCountDataProvider
     */
    public function testHandleLabelCount($handleName, $labelCount)
    {
         $this->assertSame($labelCount, 1, "Handle '{$handleName}' does not have a label or has more then one.'");
    }

    /**
     * @return array
     */
    public function handleLabelCountDataProvider()
    {
        $handles = $this->_getCodeFrontendHandles();

        $result = array();
        foreach ($handles as $handleName => $data) {
            $result[] = array($handleName, $data['label_count']);
        }
        return $result;
    }

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
        $files = Util_Files::getLayoutFiles(array(
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

        $files = Util_Files::getLayoutFiles(array(
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

    /**
     * Test is parent handle exists
     *
     * @param string $packageAndTheme
     * @param array $pageHandles
     * @dataProvider getPageTypesHandles
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testParentHandleCorrect($packageAndTheme, $pageHandles)
    {
        list($area, $package, $theme) = explode('_', $packageAndTheme);

        $failedHandles = array();
        foreach ($pageHandles as $handleName => $parentHandleName) {
            if ($parentHandleName == '') {
                continue;
            }
            try {
                $this->assertArrayHasKey($parentHandleName, $pageHandles);
            } catch(PHPUnit_Framework_ExpectationFailedException $e) {
                $failedHandles[] = sprintf( "Parent handle name %s not exist for %s", $parentHandleName, $handleName);
            }
        }

        if (!empty($failedHandles)) {
            $this->fail(
                sprintf("Area: %s\tPackage: %s\tTheme: %s\n", $area, $package, $theme)
                . implode("\n", $failedHandles)
            );
        }

    }

    /**
     * Get page types handlers filtered by packages and themes in frontend area
     *
     * @return array
     */
    public function getPageTypesHandles()
    {
        if ($this->_pageHandles) {
            return $this->_pageHandles;
        }

        $handles = array();
        foreach ($this->_getPackagesAndThemes() as $packageAndTheme) {
            $files = Util_Files::getLayoutFiles($packageAndTheme);
            $packageAndTheme['include_design'] = (int) $packageAndTheme['include_design'];
            $idPackageAndTheme = implode('_', $packageAndTheme);

            $handleNodesResult = array();
            foreach (array_keys($files) as $path) {
                $xml = simplexml_load_file($path);
                $handleNodes = $xml->xpath('/layout//*[@type="page"]') ?: array();
                /** @var $handleNode SimpleXMLElement */
                foreach ($handleNodes as $handleNode) {
                    $handleNodeName = $handleNode->getName();
                    $handleNodeAttributes = $handleNode->attributes();
                    $parentNodeName = isset($handleNodeAttributes['parent'])?
                            (string) $handleNodeAttributes['parent'] : '';

                    $handleNodesResult[$handleNodeName] = $parentNodeName;
                }
            }
            $handles[] = array($idPackageAndTheme, $handleNodesResult);
        }

        $this->_pageHandles = $handles;
        return $this->_pageHandles;
    }

    /**
     * Get all possible packages and themes in frontend area
     *
     * @return array
     */
    protected function _getPackagesAndThemes()
    {
        return array(
            array('area' => 'frontend', 'package' => 'default',    'theme' => 'default', 'include_design' => false),
            array('area' => 'frontend', 'package' => 'default',    'theme' => 'default', 'include_design' => true),
            array('area' => 'frontend', 'package' => 'default',    'theme' => 'iphone',  'include_design' => true),
            array('area' => 'frontend', 'package' => 'default',    'theme' => 'modern',  'include_design' => true),
            array('area' => 'frontend', 'package' => 'pro',        'theme' => 'default', 'include_design' => true),
            array('area' => 'frontend', 'package' => 'enterprise', 'theme' => 'default', 'include_design' => true)
        );
    }

    /**
     * Suppressing PHPMD issues because this test is complex and it is not reasonable to separate it
     *
     * @param string $file
     * @dataProvider containerDeclarationDataProvider
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testContainerDeclaration($file)
    {
        $xml = simplexml_load_file($file);
        $containers = $xml->xpath('/layout//container') ?: array();
        $errors = array();
        /** @var SimpleXMLElement $node */
        foreach ($containers as $node) {
            $nodeErrors = array();
            $attr = $node->attributes();
            if (!isset($attr['name'])) {
                $nodeErrors[] = '"name" attribute is not specified';
            } elseif (!preg_match('/^[a-z][a-z\-\_\d\.]*$/i', $attr['name'])) {
                $nodeErrors[] = 'specified value for "name" attribute is invalid';
            }
            if (!isset($attr['label']) || '' == $attr['label']) {
                $nodeErrors[] = '"label" attribute is not specified or empty';
            }
            if (isset($attr['as']) && !preg_match('/^[a-z\d\-\_]+$/i', $attr['as'])) {
                $nodeErrors[] = 'specified value for "as" attribute is invalid';
            }
            if (isset($attr['htmlTagName']) && !preg_match('/^[a-z]+$/', $attr['htmlTagName'])) {
                $nodeErrors[] = 'specified value for "htmlTagName" attribute is invalid';
            }
            if (!isset($attr['htmlTagName']) && (isset($attr['htmlId']) || isset($attr['htmlClass']))) {
                $nodeErrors[] = 'having "htmlId" or "htmlClass" attributes don\'t make sense without "htmlTagName"';
            }
            if (isset($attr['htmlId']) && !preg_match(self::HTML_ID_PATTERN, $attr['htmlId'])) {
                $nodeErrors[] = 'specified value for "htmlId" attribute is invalid';
            }
            if (isset($attr['htmlClass']) && !preg_match(self::HTML_ID_PATTERN, $attr['htmlClass'])) {
                $nodeErrors[] = 'specified value for "htmlClass" attribute is invalid';
            }
            foreach ($attr as $key => $attribute) {
                if (!in_array($key, array('name', 'label', 'as', 'htmlTagName', 'htmlId', 'htmlClass', 'module'))) {
                    $nodeErrors[] = 'unexpected attribute "' . $key . '"';
                }
            }
            if ($nodeErrors) {
                $errors[] = "\n" . $node->asXML() . "\n - " . implode("\n - ", $nodeErrors);
            }
        }
        if ($errors) {
            $this->fail(implode("\n\n", $errors));
        }
    }

    /**
     * @return array
     */
    public function containerDeclarationDataProvider()
    {
        return Util_Files::getLayoutFiles();
    }
}
