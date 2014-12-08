<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SomeModule\Model\One;

require_once __DIR__ . '/../Proxy.php';
class Test
{
    /**
     * @var \Magento\SomeModule\Model\Proxy
     */
    protected $_proxy;

    public function __construct(\Magento\SomeModule\Model\Proxy $proxy)
    {
        $this->_proxy = $proxy;
    }
}
