<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Downloadable\Controller\Adminhtml\Downloadable\Product\Edit;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class SampleTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Downloadable\Controller\Adminhtml\Downloadable\Product\Edit\Sample */
    protected $sample;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var \Magento\Downloadable\Model\Sample
     */
    protected $sampleModel;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Downloadable\Helper\File
     */
    protected $fileHelper;

    /**
     * @var \Magento\Downloadable\Helper\Download
     */
    protected $downloadHelper;


    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->request = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            array(
                'getParam',
                'getModuleName',
                'setModuleName',
                'getActionName',
                'setActionName',
                'getCookie'
            )
        );
        $this->response = $this->getMock(
            '\Magento\Framework\App\ResponseInterface',
            array(
                'setHttpResponseCode',
                'clearBody',
                'sendHeaders',
                'sendResponse',
                'setHeader'
            )
        );
        $this->fileHelper = $this->getMock(
            '\Magento\Downloadable\Helper\File',
            array(
                'getFilePath'
            ),
            array(),
            '',
            false
        );
        $this->downloadHelper = $this->getMock(
            'Magento\Downloadable\Helper\Download',
            array(
                'setResource',
                'getFilename',
                'getContentType',
                'output',
                'getFileSize',
                'getContentDisposition'
            ),
            array(),
            '',
            false
        );
        $this->sampleModel = $this->getMock(
            '\Magento\Downloadable\Controller\Adminhtml\Downloadable\Product\Edit\Sample',
            array(
                'load',
                'getId',
                'getSampleType',
                'getSampleUrl',
                'getBasePath',
                'getBaseSamplePath',
                'getSampleFile',
            ),
            array(),
            '',
            false
        );
        $this->objectManager = $this->getMock(
            '\Magento\Framework\ObjectManager\ObjectManager',
            array(
                'create',
                'get'
            ),
            array(),
            '',
            false
        );
        $this->sample = $this->objectManagerHelper->getObject(
            'Magento\Downloadable\Controller\Adminhtml\Downloadable\Product\Edit\Sample',
            array(
                'objectManager' => $this->objectManager,
                'request' => $this->request,
                'response' => $this->response
            )
        );
    }

    /**
     * Execute download sample file action
     */
    public function testExecuteFile()
    {
        $this->request->expects($this->at(0))->method('getParam')->with('id', 0)
            ->will($this->returnValue(1));
        $this->response->expects($this->once())->method('setHttpResponseCode')
            ->will($this->returnSelf());
        $this->response->expects($this->once())->method('clearBody')
            ->will($this->returnSelf());
        $this->response->expects($this->any())->method('setHeader')
            ->will($this->returnSelf());
        $this->response->expects($this->once())->method('sendHeaders')
            ->will($this->returnSelf());
        $this->objectManager->expects($this->at(1))->method('get')->with('Magento\Downloadable\Helper\File')
            ->will($this->returnValue($this->fileHelper));
        $this->objectManager->expects($this->at(2))->method('get')->with('Magento\Downloadable\Model\Sample')
            ->will($this->returnValue($this->sampleModel));
        $this->objectManager->expects($this->at(3))->method('get')->with('Magento\Downloadable\Helper\Download')
            ->will($this->returnValue($this->downloadHelper));
        $this->fileHelper->expects($this->once())->method('getFilePath')
            ->will($this->returnValue('filepath/sample.jpg'));
        $this->downloadHelper->expects($this->once())->method('setResource')
            ->will($this->returnSelf());
        $this->downloadHelper->expects($this->once())->method('getFilename')
            ->will($this->returnValue('sample.jpg'));
        $this->downloadHelper->expects($this->once())->method('getContentType')
            ->will($this->returnSelf('file'));
        $this->downloadHelper->expects($this->once())->method('getFileSize')
            ->will($this->returnValue(null));
        $this->downloadHelper->expects($this->once())->method('getContentDisposition')
            ->will($this->returnValue(null));
        $this->downloadHelper->expects($this->once())->method('output')
            ->will($this->returnSelf());
        $this->sampleModel->expects($this->once())->method('load')
            ->will($this->returnSelf());
        $this->sampleModel->expects($this->once())->method('getId')
            ->will($this->returnValue('1'));
        $this->sampleModel->expects($this->any())->method('getSampleType')
            ->will($this->returnValue('file'));
        $this->objectManager->expects($this->once())->method('create')
            ->will($this->returnValue($this->sampleModel));

        $this->sample->execute();
    }

    /**
     * Execute download sample url action
     */
    public function testExecuteUrl()
    {
        $this->request->expects($this->at(0))->method('getParam')->with('id', 0)
            ->will($this->returnValue(1));
        $this->response->expects($this->once())->method('setHttpResponseCode')
            ->will($this->returnSelf());
        $this->response->expects($this->once())->method('clearBody')
            ->will($this->returnSelf());
        $this->response->expects($this->any())->method('setHeader')
            ->will($this->returnSelf());
        $this->response->expects($this->once())->method('sendHeaders')
            ->will($this->returnSelf());
        $this->objectManager->expects($this->at(1))->method('get')->with('Magento\Downloadable\Helper\Download')
            ->will($this->returnValue($this->downloadHelper));
        $this->downloadHelper->expects($this->once())->method('setResource')
            ->will($this->returnSelf());
        $this->downloadHelper->expects($this->once())->method('getFilename')
            ->will($this->returnValue('sample.jpg'));
        $this->downloadHelper->expects($this->once())->method('getContentType')
            ->will($this->returnSelf('url'));
        $this->downloadHelper->expects($this->once())->method('getFileSize')
            ->will($this->returnValue(null));
        $this->downloadHelper->expects($this->once())->method('getContentDisposition')
            ->will($this->returnValue(null));
        $this->downloadHelper->expects($this->once())->method('output')
            ->will($this->returnSelf());
        $this->sampleModel->expects($this->once())->method('load')
            ->will($this->returnSelf());
        $this->sampleModel->expects($this->once())->method('getId')
            ->will($this->returnValue('1'));
        $this->sampleModel->expects($this->any())->method('getSampleType')
            ->will($this->returnValue('url'));
        $this->objectManager->expects($this->once())->method('create')
            ->will($this->returnValue($this->sampleModel));

        $this->sample->execute();
    }
}
