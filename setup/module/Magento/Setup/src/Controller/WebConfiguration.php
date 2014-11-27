<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\SetupInfo;

class WebConfiguration extends AbstractActionController
{
    /**
     * Directory list
     *
     * @var DirectoryList
     */
    private $dirList;

    /**
     * Constructor
     *
     * @param DirectoryList $dirList
     */
    public function __construct(DirectoryList $dirList)
    {
        $this->dirList = $dirList;
    }

    /**
     * Displays web configuration form
     *
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $setupInfo = new SetupInfo($_SERVER);
        $projectRoot = $this->dirList->getRoot();
        $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        if ($setupInfo->isAvailable($this->dirList->getRoot())) {
            $urlPath = '';
        } else {
            $urlPath = substr($docRoot, strlen($projectRoot));
        }
        $view = new ViewModel(['autoBaseUrl' => 'http://' . $_SERVER['HTTP_HOST'] . $urlPath . '/']);
        $view->setTerminal(true);
        return $view;
    }
}
