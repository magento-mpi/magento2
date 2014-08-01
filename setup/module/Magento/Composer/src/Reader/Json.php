<?php
/**
 * Module declaration reader. Reads module.xml declaration files from module /etc directories.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Composer\Reader;

use Magento\Composer\FileResolver;

class Json
{
    /**
     * @var \Magento\Composer\FileResolver
     */
    protected $fileResolver;

    /**
     * The name of file that stores configuration
     *
     * @var string
     */
    protected $fileName;

    /**
     * @param FileResolver $fileResolver
     * @param string $fileName
     */
    public function __construct(
        FileResolver $fileResolver,
        $fileName = 'composer.json'
    ) {
        $this->fileResolver = $fileResolver;
        $this->fileName = $fileName;
    }

    /**
     * @return array
     */
    public function read()
    {
        $fileList = $this->fileResolver->get($this->fileName);
        if (!count($fileList)) {
            return [];
        }
        return $this->readFiles($fileList);
    }

    /**
     * @param array $fileList
     * @return array
     * @throws \Exception
     */
    protected function readFiles($fileList)
    {
        $result = [];
        foreach ($fileList as $content) {
            $result[] = json_decode($content);
        }
        return $result;
    }
}
