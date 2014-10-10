<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\TestFramework\App;

use Magento\Framework\App\Filesystem\DirectoryList;

class Filesystem extends \Magento\Framework\App\Filesystem
{
    /**
     * Overridden paths
     *
     * @var string[]
     */
    private $paths = [];

    /**
     * {@inheritdoc}
     */
    public function getPath($code = DirectoryList::ROOT)
    {
        return $this->getOverriddenPath($code, parent::getPath($code));
    }

    /**
     * {@inheritdoc}
     */
    protected function getDirPath($code)
    {
        return $this->getOverriddenPath($code, parent::getDirPath($code));
    }

    /**
     * {@inheritdoc}
     */
    protected function getSysTmpPath()
    {
        return $this->getOverriddenPath(self::SYS_TMP, parent::getSysTmpPath());
    }

    /**
     * Overrides a path to directory for testing purposes
     *
     * @param string $code
     * @param string $value
     * @return void
     */
    public function overridePath($code, $value)
    {
        $this->paths[$code] = str_replace('\\', '/', $value);
        unset($this->readInstances[$code]);
        unset($this->writeInstances[$code]);
    }

    /**
     * Looks up an overridden directory path
     *
     * @param string $code
     * @param string $original
     * @return string
     */
    private function getOverriddenPath($code, $original)
    {
        if (array_key_exists($code, $this->paths)) {
            return $this->paths[$code];
        }
        return $original;
    }
}
