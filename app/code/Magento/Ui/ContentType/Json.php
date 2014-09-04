<?php
/**
 * {license}
 */

namespace Magento\Ui\ContentType;

use Magento\Ui\UiInterface;

/**
 * Class Json
 * @package Magento\Ui\ContentType
 */
class Json implements ContentTypeInterface
{
    /**
     * @param UiInterface $ui
     * @param array $data
     * @param array $configuration
     * @return string
     */
    public function render(UiInterface $ui, array $data, array $configuration)
    {
        return json_encode($this->getDataJson($data, $ui));
    }

    /**
     * @param array $data
     * @param UiInterface $ui
     * @return array
     */
    protected function getDataJson(array $data, UiInterface $ui)
    {
        $result = [
            'configuration' => $ui->getConfiguration()
        ];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if (method_exists($value, 'toJson')) {
                    $result[$key] = $value->toJson();
                } else {
                    $result[$key] = $this->objectToJson($value);
                }
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @param $object
     * @return string
     */
    protected function objectToJson($object)
    {
        return '';
    }
}
