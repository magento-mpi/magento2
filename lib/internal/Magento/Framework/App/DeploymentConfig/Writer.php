<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Deployment configuration writer
 */
class Writer
{
    /**
     * Deployment config reader
     *
     * @var Reader
     */
    private $reader;

    /**
     * Application filesystem
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Constructor
     *
     * @param Reader $reader
     * @param Filesystem $filesystem
     */
    public function __construct(Reader $reader, Filesystem $filesystem)
    {
        $this->reader = $reader;
        $this->filesystem = $filesystem;
    }

    /**
     * Creates the deployment configuration file
     *
     * Will overwrite a file, if it exists.
     *
     * @param SegmentInterface[] $segments
     * @return void
     */
    public function create($segments)
    {
        $data = [];
        foreach ($segments as $segment) {
            $data[$segment->getKey()] = $segment->getData();
        }
        $this->write($data);
    }

    /**
     * Update data in the configuration file using specified segment object
     *
     * @param SegmentInterface $segment
     * @return void
     */
    public function update(SegmentInterface $segment)
    {
        $key = $segment->getKey();
        $data = $this->reader->load();
        $data[$key] = $segment->getData();
        $this->write($data);
    }

    /**
     * Persists the data into file
     *
     * @param array $data
     * @return void
     */
    private function write($data)
    {
        $contents = '<?php' . PHP_EOL . 'return ' . var_export($data, true) . ';' . PHP_EOL;
        $this->filesystem->getDirectoryWrite(DirectoryList::CONFIG)->writeFile($this->reader->getFile(), $contents);
    }
}
