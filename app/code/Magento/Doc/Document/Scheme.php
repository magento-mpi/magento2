<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Document;

use Magento\Doc\Document\Scheme\Reader;

class Scheme
{
    /**
     * Scheme reader
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
     * @param string $fileName
     * @param null $scope
     * @return array
     */
    public function get($fileName, $scope = null)
    {
        return $this->reader->read($fileName, $scope);
    }
}
