<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Controller\Adminhtml\Search\System\Config\Testconnection;

use Magento\Backend\App\Action;
use Magento\Framework\Filesystem\File\ReadFactory;

class Ping extends \Magento\Backend\App\Action
{
    /** @var ReadFactory */
    protected $fileReadFactory;

    /**
     * @param Action\Context $context
     * @param ReadFactory $fileReadFactory
     */
    public function __construct(Action\Context $context, ReadFactory $fileReadFactory)
    {
        parent::__construct($context);
        $this->fileReadFactory = $fileReadFactory;
    }

    /**
     * Check for connection to server
     *
     * @return void
     */
    public function execute()
    {
        if (!isset(
            $_REQUEST['host']
        ) || !($host = $_REQUEST['host']) || !isset(
            $_REQUEST['port']
        ) || !($port = (int)$_REQUEST['port']) || !isset(
            $_REQUEST['path']
        ) || !($path = $_REQUEST['path'])
        ) {
            echo 0;
            exit;
        }

        $path = $host . ':' . $port . '/' . $path . '/admin/ping';
        $httpResource = $this->fileReadFactory->create($path, \Magento\Framework\Filesystem\DriverPool::HTTP);

        if (isset($_REQUEST['timeout'])) {
            $timeout = (int)$_REQUEST['timeout'];
            if ($timeout < 0) {
                $timeout = -1;
            }
        } else {
            $timeout = 0;
        }

        $context = stream_context_create(['http' => ['method' => 'HEAD', 'timeout' => $timeout]]);

        // attempt a HEAD request to the solr ping page
        $ping = $httpResource->readAll();

        // result is false if there was a timeout
        // or if the HTTP status was not 200
        if ($ping !== false) {
            echo 1;
        } else {
            echo 0;
        }
    }
}
