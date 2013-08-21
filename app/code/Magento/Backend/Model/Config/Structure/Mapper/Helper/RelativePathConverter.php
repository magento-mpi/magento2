<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper that can convert relative paths from system.xml to absolute
 */
class Magento_Backend_Model_Config_Structure_Mapper_Helper_RelativePathConverter
{
    /**
     * Convert relative path from system.xml to absolute
     *
     * @param string $nodePath
     * @param string $relativePath
     * @return string
     * @throws InvalidArgumentException
     */
    public function convert($nodePath, $relativePath)
    {
        $nodePath = trim($nodePath);
        $relativePath = trim($relativePath);

        if (empty($nodePath) || empty($relativePath)) {
            throw new InvalidArgumentException('Invalid arguments');
        }

        $relativePathParts = explode('/', $relativePath);
        $pathParts = explode('/', $nodePath);

        $relativePartsCount = count($relativePathParts);
        $pathPartsCount = count($pathParts);

        if ($relativePartsCount === 1 && $pathPartsCount > 1) {
            $relativePathParts = array_pad($relativePathParts, -$pathPartsCount, '*');
        }

        $realPath = array();
        foreach ($relativePathParts as $index => $path) {
            if ($path === '*') {
                if (false == array_key_exists($index, $pathParts)) {
                    throw new InvalidArgumentException(
                        sprintf('Invalid relative path %s in %s node', $relativePath, $nodePath));
                }
                $path = $pathParts[$index];
            }
            $realPath[$index] = $path;
        }

        return implode('/', $realPath);
    }
}
