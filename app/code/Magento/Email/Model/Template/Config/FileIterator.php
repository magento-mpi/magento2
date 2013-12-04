<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Model\Template\Config;

/**
 * Class FileIterator
 */
class FileIterator extends \Magento\Config\FileIterator
{
    /**
     * @var \Magento\Module\Dir\ReverseResolver
     */
    protected $_moduleDirResolver;

    /**
     * @param \Magento\Filesystem\Directory\ReadInterface $directory
     * @param \Magento\Module\Dir\ReverseResolver         $dirResolver
     * @param array                                       $paths
     */
    public function __construct(
        \Magento\Filesystem\Directory\ReadInterface $directory,
        \Magento\Module\Dir\ReverseResolver $dirResolver,
        array $paths
    ) {
        parent::__construct($directory, $paths);
        $this->_moduleDirResolver = $dirResolver;
    }

    /**
     * @return string
     * @throws \UnexpectedValueException
     */
    function current()
    {
        if (!isset($this->cached[$this->key()])) {
            $contents = $this->directoryRead->readFile($this->key());
            $path = $this->directoryRead->getAbsolutePath($this->key());
            $moduleName = $this->_moduleDirResolver->getModuleName($path);
            if (!$moduleName) {
                throw new \UnexpectedValueException(
                    sprintf("Unable to determine a module, file '%s' belongs to.", $this->key())
                );
            }
            $contents = str_replace('<template ', '<template module="' . $moduleName . '" ', $contents);
            $this->cached[$this->key()] = $contents;
        }
        return $this->cached[$this->key()];

    }
}
