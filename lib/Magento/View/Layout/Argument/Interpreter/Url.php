<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Layout\Argument\Interpreter;

use Magento\UrlInterface;
use Magento\Data\Argument\InterpreterInterface;

/**
 * Interpreter that builds URL by input path and optional parameters
 */
class Url implements InterpreterInterface
{
    /**
     * @var UrlInterface
     */
    private $urlResolver;

    /**
     * @var NamedParams
     */
    private $paramsInterpreter;

    /**
     * @param UrlInterface $urlResolver
     * @param NamedParams $paramsInterpreter
     */
    public function __construct(UrlInterface $urlResolver, NamedParams $paramsInterpreter)
    {
        $this->urlResolver = $urlResolver;
        $this->paramsInterpreter = $paramsInterpreter;
    }

    /**
     * {@inheritdoc}
     * @return string
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (!isset($data['path'])) {
            throw new \InvalidArgumentException('URL path is missing.');
        }
        $urlPath = $data['path'];
        $urlParams = $this->paramsInterpreter->evaluate($data);
        return $this->urlResolver->getUrl($urlPath, $urlParams);
    }
}
