<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ui\DataProvider\Config;

use Magento\Framework\Config\ConverterInterface;
use Zend\XmlRpc\Generator\DomDocument;

/**
 * Class Converter
 */
class Converter implements ConverterInterface
{
    /**
     * Transform Xml to array
     *
     * @param $source
     * @return array
     */
    protected function toArray($source)
    {
        /** @var $source \DOMDocument */
        $result = array();
        if ($source->hasAttributes()) {
            $attrs = $source->attributes;
            foreach ($attrs as $attr) {
                $result['@attributes'][$attr->name] = $attr->value;
            }
        }

        if ($source->hasChildNodes()) {
            $children = $source->childNodes;
            $groups = array();
            foreach ($children as $child) {
                if ($child->nodeType == XML_TEXT_NODE || $child->nodeType == XML_COMMENT_NODE) {
                    continue;
                }
                if (!isset($result[$child->nodeName])) {
                    $result[$child->nodeName] = $this->toArray($child);
                } else {
                    if (!isset($groups[$child->nodeName])) {
                        $result[$child->nodeName] = [$result[$child->nodeName]];
                        $groups[$child->nodeName] = 1;
                    }
                    $result[$child->nodeName][] = $this->toArray($child);
                }
            }
        }
        return $result;
    }

    /**
     * Convert configuration
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $data = [];
        $output = $this->toArray($source);
        foreach ($output['config']['fieldset'] as $fieldset) {
            $data[$fieldset['@attributes']['name']] = [
                'name' => $fieldset['@attributes']['name'],
                'dataset' => $fieldset['@attributes']['dataset'],
            ];
            $fields = [];
            foreach ($fieldset['fields']['field'] as $field) {
                foreach ($field['@attributes'] as $key => $value) {
                    $fields[$field['@attributes']['name']][$key] = $value;
                }
                if ($field['@attributes']['datatype'] == 'lookup') {
                    $fields[$field['@attributes']['name']]['reference'] =  [
                        'target' => $field['reference']['@attributes']['target'],
                        'target_field' => $field['reference']['@attributes']['target_field'],
                        'referenced_field' => $field['reference']['@attributes']['referenced_field'],
                        'needed_field' => $field['reference']['@attributes']['needed_field']
                    ];
                }
            }
            $data[$fieldset['@attributes']['name']]['fields'] = $fields;
            if (!empty($fieldset['references'])) {
                foreach ($fieldset['references'] as $reference) {
                    $data[$reference['@attributes']['target']]['children'][$fieldset['@attributes']['name']][] = [
                        'target_field' => $reference['@attributes']['target_field'],
                        'referenced_field' => $reference['@attributes']['referenced_field']
                    ];
                }
            }
        }
        return $data;
    }
}

