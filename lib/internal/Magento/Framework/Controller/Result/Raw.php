<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Controller\Result;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * A result that contains raw response - may be good for passing through files,
 * returning result of downloads or some other binary contents
 */
class Raw implements ResultInterface
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function renderResult(ResponseInterface $response)
    {
        $response->setBody($this->contents);
        return $this;
    }
}
