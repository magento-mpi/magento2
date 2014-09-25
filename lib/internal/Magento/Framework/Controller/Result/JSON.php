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
 * A possible implementation of JSON response type (instead of hardcoding json_encode() all over the place)
 * Actual for controller actions that serve ajax requests
 */
class JSON implements ResultInterface
{
    /**
     * @var string
     */
    private $json;

    /**
     * Set json data as array
     *
     * @param array $array
     */
    public function setArray(array $array)
    {
        $this->json = $array;
    }

    /**
     * Set json data as object
     *
     * @param \StdClass $object
     */
    public function setObject(\StdClass $object)
    {
        $this->json = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function renderResult(ResponseInterface $response)
    {
        $response->setBody(json_encode($this->json));
        return $this;
    }
}
