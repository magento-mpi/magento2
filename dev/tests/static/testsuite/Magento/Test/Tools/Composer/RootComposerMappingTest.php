<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer;

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
        $paths = file(BP . '/dev/tools/Magento/Tools/Composer/etc/magento_components_list.txt', FILE_IGNORE_NEW_LINES);
        $counter = 0;
        for($i=0; $i<count($paths); $i++){
            if (file_exists(BP . '/' . $paths[$i])) {
                $counter++;
            }
        }

        $this->assertEquals(count($paths), $counter);

        //Checking existence of customizable paths
        $paths = file(BP .
            '/dev/tools/Magento/Tools/Composer/etc/magento_customizable_paths.txt', FILE_IGNORE_NEW_LINES);
        $counter = 0;
        for($i=0; $i<count($paths); $i++){
            if (file_exists(BP . '/' . str_replace('*', '', $paths[$i]))) {
                $counter++;
            }
        }

        $this->assertEquals(count($paths), $counter);
    }
}