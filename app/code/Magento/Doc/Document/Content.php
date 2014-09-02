<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document;

use Magento\Doc\Document\Content\Reader;

class Content
{
    /**
     * Content reader
     *
     * @var Reader
     */
    protected $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Load and merge content files of the given name from all modules
     *
     * @param string $fileName
     * @param null $scope
     * @return string
     */
    public function get($fileName, $scope = null)
    {
        return $this->reader->read($fileName, $scope);
    }
}
