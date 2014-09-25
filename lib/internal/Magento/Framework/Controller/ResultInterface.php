<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Controller;

use Magento\Framework\App\ResponseInterface;

/**
 * An abstraction of result that controller actions must return
 * The point of this kind of object is to encapsulate all information/objects relevant to the result
 * and be able to set it to the HTTP response
 */
interface ResultInterface
{
    /**
     * Render result and set to response
     *
     * @param ResponseInterface $response
     * @return $this
     */
    public function renderResult(ResponseInterface $response);
}
