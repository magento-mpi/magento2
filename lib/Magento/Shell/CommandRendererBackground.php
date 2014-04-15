<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shell
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shell;

use Magento\OsInfo;

class CommandRendererBackground extends CommandRenderer
{
    /**
     * @var \Magento\OsInfo
     */
    protected $osInfo;

    /**
     * @param OsInfo $osInfo
     */
    public function __construct(OsInfo $osInfo)
    {
        $this->osInfo = $osInfo;
    }

    /**
     * Render command with arguments
     *
     * @param string $command
     * @param array $arguments
     * @return string
     */
    public function render($command, array $arguments = array())
    {
        $command = parent::render($command, $arguments);
        return $this->osInfo->isWindows() ?
            'start /B "magento background task" ' . $command
            : $command . ' > /dev/null &';
    }
}
