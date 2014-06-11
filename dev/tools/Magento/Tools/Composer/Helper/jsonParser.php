<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Helper;

/**
* Helper class for parsing a json file and returning name and version information
*/
class JsonParser
{
    /**
     * Parses a json file
     *
     * @param string $path
     * @param array $info
     * @return void
    */
    public static  function parseJsonFiles($path, array &$info = array())
    {
        $json = file_get_contents($path);
        $replaceChars = array("[", "]");
        $jsonIterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator(json_decode(str_replace($replaceChars, "", $json), true)),
            \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($jsonIterator as $key => $val) {
            if (is_array($val)) {
                continue;
            } else {
                if (($key === "name") && strpos($val, '/')) {
                    $info["name"] = $val;
                }
                if ($key === "version") {
                    $info["version"] = $val;
                }
            }
        }
    }
}