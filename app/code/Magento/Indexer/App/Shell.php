<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\App;

class Shell implements \Magento\AppInterface
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    protected $entryFileName;

    /**
     * @var \Magento\Indexer\App\Shell\ErrorHandler
     */
    protected $errorHandler;

    /**
     * @var \Magento\Indexer\Model\ShellFactory
     */
    protected $shellFactory;

    /**
     * @param string $entryFileName
     * @param \Magento\Indexer\Model\ShellFactory $shellFactory
     * @param Shell\ErrorHandler $errorHandler
     */
    public function __construct(
        $entryFileName,
        \Magento\Indexer\Model\ShellFactory $shellFactory,
        Shell\ErrorHandler $errorHandler
    ) {
        $this->entryFileName = $entryFileName;
        $this->shellFactory = $shellFactory;
        $this->errorHandler = $errorHandler;
    }

    /**
     * Run application
     *
     * @return int
     */
    public function execute()
    {
        /** @var $shell \Magento\Indexer\Model\Shell */
        $shell = $this->shellFactory->create(array('entryPoint' => $this->entryFileName));
        $shell->run();
        if ($shell->hasErrors()) {
            $this->errorHandler->terminate(1);
        }
        return 0;
    }
}
