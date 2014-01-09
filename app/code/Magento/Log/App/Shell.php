<?php
/**
 * Log shell application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\App;

use Magento\LauncherInterface;

class Shell implements LauncherInterface
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    protected $_entryFileName;

    /**
     * @var \Magento\Log\Model\ShellFactory
     */
    protected $_shellFactory;

    /**
     * @param string $entryFileName
     * @param \Magento\Log\Model\ShellFactory $shellFactory
     */
    public function __construct(
        $entryFileName,
        \Magento\Log\Model\ShellFactory $shellFactory
    ) {
        $this->_entryFileName = $entryFileName;
        $this->_shellFactory = $shellFactory;
    }


    /**
     * Run application
     *
     * @return int
     */
    public function launch()
    {
        /** @var $shell \Magento\Log\Model\Shell */
        $shell = $this->_shellFactory->create(array('entryPoint' => $this->_entryFileName));
        $shell->run();
        return 0;
    }
}
