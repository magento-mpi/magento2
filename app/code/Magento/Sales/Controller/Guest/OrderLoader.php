<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Guest;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;

class OrderLoader implements OrderLoaderInterface
{
    /**
     * @var \Magento\Sales\Helper\Guest
     */
    protected $guestHelper;

    /**
     * @param \Magento\Sales\Helper\Guest $guestHelper
     */
    public function __construct(
        \Magento\Sales\Helper\Guest $guestHelper
    ) {
        $this->guestHelper = $guestHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        return $this->guestHelper->loadValidOrder($request, $response);
    }
}
