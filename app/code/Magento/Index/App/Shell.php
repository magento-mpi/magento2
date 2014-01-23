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

use Magento\App\Console\Response;
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
     * @var \Magento\Index\Model\ShellFactory
     */
    protected $_shellFactory;

    /**
     * @var \Magento\App\Console\Response
     */
    protected $_response;

    /**
     * @param string $entryFileName
     * @param \Magento\Index\Model\ShellFactory $shellFactory
     * @param Response $response
     */
    public function __construct(
        $entryFileName,
        \Magento\Index\Model\ShellFactory $shellFactory,
        Response $response
    ) {
        $this->_entryFileName = $entryFileName;
        $this->_shellFactory = $shellFactory;
        $this->_response = $response;
    }

    /**
     * Run application
     *
     * @return \Magento\App\ResponseInterface
     */
    public function launch()
    {
        /** @var $shell \Magento\Index\Model\Shell */
        $shell = $this->_shellFactory->create(array('entryPoint' => $this->_entryFileName));
        $shell->run();
        if ($shell->hasErrors()) {
            $this->_response->setCode(-1);
        } else {
            $this->_response->setCode(0);
        }
        return $this->_response;
    }
}
