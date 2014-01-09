<?php
/**
 * Index shell application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\App;

use Magento\LauncherInterface,
    Magento\Index\App\Shell\ErrorHandler;

class Shell implements LauncherInterface
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    protected $_entryFileName;

    /**
     * @var \Magento\Index\App\Shell\ErrorHandler
     */
    protected $_errorHandler;

    /**
     * @var \Magento\Index\Model\ShellFactory
     */
    protected $_shellFactory;

    /**
     * @param string $entryFileName
     * @param \Magento\Index\Model\ShellFactory $shellFactory
     * @param ErrorHandler $errorHandler
     */
    public function __construct(
        $entryFileName,
        \Magento\Index\Model\ShellFactory $shellFactory,
        ErrorHandler $errorHandler
    ) {
        $this->_entryFileName = $entryFileName;
        $this->_shellFactory = $shellFactory;
        $this->_errorHandler = $errorHandler;
    }

    /**
     * Run application
     *
     * @return int
     */
    public function launch()
    {
        /** @var $shell \Magento\Index\Model\Shell */
        $shell = $this->_shellFactory->create(array('entryPoint' => $this->_entryFileName));
        $shell->run();
        if ($shell->hasErrors()) {
            $this->_errorHandler->terminate(1);
        }
        return 0;
    }
}
