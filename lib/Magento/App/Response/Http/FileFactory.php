<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Response\Http;

class FileFactory
{
    /**
     * @var \Magento\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param \Magento\App\ResponseFactory $responseFactory
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(\Magento\App\ResponseFactory $responseFactory, \Magento\Filesystem $filesystem)
    {
        $this->_responseFactory = $responseFactory;
        $this->_filesystem = $filesystem;
    }

    /**
     * Declare headers and content file in response for file download
     *
     * @param string $fileName
     * @param string|array $content set to null to avoid starting output, $contentLength should be set explicitly in
     *                              that case
     * @param string $contentType
     * @param int $contentLength    explicit content length, if strlen($content) isn't applicable
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @return \Magento\App\ActionInterface
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function create($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null)
    {
        $filesystem = $this->_filesystem;
        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                throw new \InvalidArgumentException("Invalid arguments. Keys 'type' and 'value' are required.");
            }
            if ($content['type'] == 'filename') {
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = $filesystem->getFileSize($file);
            }
        }

        $response = $this->_responseFactory->create();
        $response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength, true)
            ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"', true)
            ->setHeader('Last-Modified', date('r'), true);

        if (!is_null($content)) {
            if ($isFile) {
                $response->clearBody();
                $response->sendHeaders();

                if (!$filesystem->isFile($file)) {
                    throw new \Exception(__('File not found'));
                }
                $stream = $filesystem->createAndOpenStream($file, 'r');
                while ($buffer = $stream->read(1024)) {
                    print $buffer;
                }
                flush();
                $stream->close();
                if (!empty($content['rm'])) {
                    $filesystem->delete($file);
                }

                exit(0);
            } else {
                $response->setBody($content);
            }
        }
        return $response;
    }
}