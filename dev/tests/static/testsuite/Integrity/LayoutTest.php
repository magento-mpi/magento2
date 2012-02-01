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
        $handles = array();

        /*
         * Collect counts of handle labels that declared in code
         */
        $files = Util_Files::getLayoutFiles(array(
            'include_design' => false,
            'area' => 'frontend'
        ));
        foreach ($files as $path => $details) {
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

        /*
         * Collect handle labels that declared in design only
         */
        $files = Util_Files::getLayoutFiles(array(
            'include_code' => false,
            'area' => 'frontend'
        ));
        foreach ($files as $path => $details) {
            $xml = simplexml_load_file($path);
            $handleNodes = $xml->xpath('/layout/*') ?: array();
            foreach ($handleNodes as $handleNode) {
                if (!isset($handles[$handleNode->getName()])) {
                    $handles[$handleNode->getName()] = 0;
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
