<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

use Magento\Ui\UiInterface;
use Magento\Framework\Object;
use Magento\Framework\Xml\Generator;

/**
 * Class Xml
 */
class Xml implements ContentTypeInterface
{
    /**
     * @var \Magento\Framework\Xml\Generator
     */
    protected $generator;

    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param UiInterface $ui
     * @param array $data
     * @param array $configuration
     * @return string
     */
    public function render(UiInterface $ui, array $data, array $configuration)
    {
        $result = [
            'configuration' => $ui->getConfiguration()
        ];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if (method_exists($value, 'toJson')) {
                    $result[$key] = $value->toJson();
                } else {
                    $result[$key] = $this->objectToXml($value);
                }
            } else {
                $result[$key] = $value;
            }
        }
        return $this->generator->arrayToXml($result);
    }

    /**
     * @param Object $object
     * @return string
     */
    protected function objectToXml(Object $object)
    {
        return '';
    }
}
