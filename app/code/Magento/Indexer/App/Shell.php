<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\App;

class Shell implements \Magento\LauncherInterface
{
    /**
     * Filename of the entry point script
     *
     * @var string
     */
    protected $entryFileName;

    /**
     * @var \Magento\App\Console\Response
     */
    protected $response;

    /**
     * @var \Magento\Indexer\Model\ShellFactory
     */
    protected $shellFactory;

    /**
     * @param $entryFileName
     * @param \Magento\Indexer\Model\ShellFactory $shellFactory
     * @param \Magento\App\Console\Response $response
     */
    public function __construct(
        $entryFileName,
        \Magento\Indexer\Model\ShellFactory $shellFactory,
        \Magento\App\Console\Response $response
    ) {
        $this->entryFileName = $entryFileName;
        $this->shellFactory = $shellFactory;
        $this->response = $response;
    }

    /**
     * Run application
     *
     * @return \Magento\App\ResponseInterface
     */
    public function launch()
    {
        /** @var $shell \Magento\Indexer\Model\Shell */
        $shell = $this->shellFactory->create(array('entryPoint' => $this->entryFileName));
        $shell->run();
        if ($shell->hasErrors()) {
            $this->response->setCode(-1);
        } else {
            $this->response->setCode(0);
        }
        return $this->response;
    }
}
