<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Layout_Helper
{
    public function getModuleDir($path)
    {
        list($moduleDir,) = $this->_separateIntoModuleAndRelName($path);
        return $moduleDir;
    }

    public function getRelName($path)
    {
        list(,$relName) = $this->_separateIntoModuleAndRelName($path);
        return $relName;
    }

    protected function _separateIntoModuleAndRelName($path)
    {
        if (!preg_match('#(.*/app/code/[^/]+/[^/]+/view/[^/]+|.*/app/design/[^/]+/[^/]+/[^/]+/[^/]+)/(.*)#',
            $path, $matches)
        ) {
            throw new Exception("Couldn't find module directory in path {$path}");
        }
        $dir = $matches[1];
        $relName = $matches[2];
        return array($dir, $relName);
    }
}
