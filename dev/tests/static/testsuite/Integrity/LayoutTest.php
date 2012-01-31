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
     * Check count of layout handle labels that described in modules for frontend area
     *
     * @dataProvider handleLabelCountDataProvider
     */
    public function testHandleLabelCount($handleName, $labelCount)
    {
         $this->assertSame($labelCount, 1, "Handle '{$handleName} does not have a label or has more then one.'");
    }

    /**
     * @return array
     */
    public function handleLabelCountDataProvider()
    {
        $root = PATH_TO_SOURCE_CODE;
        $pool = $namespace = $module = '*';
        $files = glob(
            "{$root}/app/code/{$pool}/{$namespace}/{$module}/view/frontend/*.xml",
            GLOB_NOSORT | GLOB_BRACE
        );

        $handles = array();
        foreach ($files as $path) {
            $xml = simplexml_load_file($path);
            $handleNodes = $xml->xpath('/layout/*') ?: array();
            foreach ($handleNodes as $handleNode) {
                $isLabel = $handleNode->xpath('label');
                if (isset($handles[$handleNode->getName()])) {
                    $handles[$handleNode->getName()] = $handles[$handleNode->getName()] + (int)$isLabel;
                } else {
                    $handles[$handleNode->getName()] = (int)$isLabel;
                }
            }
        }

        $result = array();
        foreach ($handles as $handleName => $labelCount) {
            $result[] = array(
                $handleName,
                $labelCount
            );
        }
        return $result;
    }
}
