<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\File\Transfer\Adapter;

class Http
{
    /**
     * @var \Zend_Controller_Response_Http
     */
    private $response;

    /**
     * @var \Magento\File\Mime
     */
    private $mime;

    /**
     * @param \Zend_Controller_Response_Http $response
     * @param \Magento\File\Mime $mime
     */
    public function __construct(\Zend_Controller_Response_Http $response, \Magento\File\Mime $mime)
    {
        $this->response = $response;
        $this->mime = $mime;
    }

    /**
     * Send the file to the client (Download)
     *
     * @param  string|array $options Options for the file(s) to send
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return void
     */
    public function send($options = null)
    {
        if (is_string($options)) {
            $filepath = $options;
        } else if (is_array($options) && isset($options['filepath'])) {
            $filepath = $options['filepath'];
        } else {
            throw new \InvalidArgumentException("Filename is not set.");
        }

        if (!is_file($filepath) || !is_readable($filepath)) {
            throw new \InvalidArgumentException("File '{$filepath}' does not exists.");
        }

        $mimeType = $this->mime->getMimeType($filepath);

        $this->response->setHeader('Content-length', filesize($filepath));
        $this->response->setHeader('Content-Type', $mimeType);

        $this->response->sendHeaders();

        $handle = fopen($filepath, 'r');
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                echo $buffer;
            }
            if (!feof($handle)) {
                throw new \UnexpectedValueException("Unexpected end of file");
            }
            fclose($handle);
        }
    }
}
