<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Doc\Document;

use Magento\Doc\Document\Outline\Reader;

class Outline
{
    /**
     * Outline reader
     *
     * @var bool
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
     * Load and merge outline files of the given name from all modules
     *
     * @param string $fileName
     * @param null $scope
     * @return array
     */
    public function get($fileName, $scope = null)
    {
        return $this->reader->read($fileName, $scope);
    }
}
