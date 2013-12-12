<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Asset\Plugin;

class CleanMergedJsCss
{
    /**
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $database;

    /**
     * @var \Magento\App\Dir
     */
    protected $dirs;

    /**
     * @param \Magento\Core\Helper\File\Storage\Database $database
     * @param \Magento\App\Dir $dirs
     */
    public function __construct(
        \Magento\Core\Helper\File\Storage\Database $database,
        \Magento\App\Dir $dirs
    ) {
        $this->database = $database;
        $this->dirs = $dirs;
    }

    /**
     * Clean files in database on cleaning merged assets
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     */
    public function aroundCleanMergedJsCss(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $invocationChain->proceed($arguments);

        $mergedDir = $this->dirs->getDir(\Magento\App\Dir::PUB_VIEW_CACHE)
            . '/' . \Magento\View\Asset\Merged::PUBLIC_MERGE_DIR;
        $this->database->deleteFolder($mergedDir);
    }
}
