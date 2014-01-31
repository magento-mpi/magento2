<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\Session;

/**
 * Class VaryPlugin
 */
class VaryPlugin
{

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     *
     * @param \Magento\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\App\ResponseInterface $response
    ){
        $this->response = $response;
    }

    /**
     * Returns vary key
     *
     * @param $methodName
     * @return string
     */
    protected function getKey($methodName)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", str_replace('set', '', $methodName)));
    }

    public function around__call($arguments)
    {
//        $varyKey = $this->getVaryKey($arguments[0]);

        //var_dump($arguments[0]);
        return $arguments;
    }

    public function after__call($arguments)
    {
        return $arguments;
    }
}
