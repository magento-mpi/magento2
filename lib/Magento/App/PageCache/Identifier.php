<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\PageCache;

/**
 * Page unique identifier
 */
class Identifier
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param \Magento\App\Request\Http $request
     */
    public function __construct(\Magento\App\Request\Http $request)
    {
        $data = array($request->getRequestUri(), $request->get(\Magento\App\Response\Http::COOKIE_VARY_STRING));
        $this->value = md5(serialize($data));
    }

    /**
     * Return unique page identifier
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
