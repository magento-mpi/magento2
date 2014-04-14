<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\File\Storage;

use Magento\App\Response\Http;

class Response extends Http
{
    /**
     * @var \Magento\File\Transfer\Adapter\Http
     */
    protected $_transferAdapter;

    /**
     * Full path to file
     *
     * @var string
     */
    protected $_filePath;

    /**
     * Constructor
     *
     * @param \Magento\Stdlib\Cookie              $cookie
     * @param \Magento\App\Http\Context           $context
     * @param \Magento\File\Transfer\Adapter\Http $transferAdapter
     */
    public function __construct(
        \Magento\Stdlib\Cookie $cookie,
        \Magento\App\Http\Context $context,
        \Magento\File\Transfer\Adapter\Http $transferAdapter
    ) {
        parent::__construct($cookie, $context);
        $this->_transferAdapter = $transferAdapter;
    }

    /**
     * Send response
     *
     * @return void
     */
    public function sendResponse()
    {
        if ($this->_filePath && $this->getHttpResponseCode() == 200) {
            $this->_transferAdapter->send($this->_filePath);
        } else {
            parent::sendResponse();
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function setFilePath($path)
    {
        $this->_filePath = $path;
    }
}
