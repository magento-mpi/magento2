<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer;

use \Magento\Tools\Composer\Package\Reader;


/**
 * Class RootComposerMappingTest
 */
class RootComposerMappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test existence of paths for marshalling
     * @return void
     */
    public function testMapping()
    {
        //Checking existence of composer components
        $reader = new Reader(BP . '/dev/tools/Magento/Tools/Composer');
        $patterns = $reader->getPatterns();
        $counter = 0;
        for ($i = 0; $i < count($patterns); $i++) {
            if (file_exists(BP . '/' . $patterns[$i])) {
                $counter++;
            }
        }

        $this->assertEquals(count($patterns), $counter);

        //Checking existence of customizable paths
        $paths = file(BP .
            '/dev/tools/Magento/Tools/Composer/etc/magento_customizable_paths.txt', FILE_IGNORE_NEW_LINES);
        $counter = 0;
        for ($i = 0; $i < count($paths); $i++) {
            if (file_exists(BP . '/' . str_replace('*', '', $paths[$i]))) {
                $counter++;
            }
        }

        $this->assertEquals(count($paths), $counter);
    }
}