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
     * @var \Magento\Eav\Model\Entity\TypeFactory
     */
    protected $entityTypeFactory;

    /**
     * @param \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory
     */
    public function __construct(\Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory)
    {
        $this->entityTypeFactory = $entityTypeFactory;
    }

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
                if (in_array($child->nodeName, ['validate', 'filter', 'readonly'])) {
                    if (!isset($result[$child->nodeName])) {
                        $result[$child->nodeName] = [];
                    }
                    $result[$child->nodeName][] = $this->toArray($child);
                } else {
                    if (isset($result[$child->nodeName])) {
                        if (!isset($groups[$child->nodeName])) {
                            $result[$child->nodeName] = [$result[$child->nodeName]];
                            $groups[$child->nodeName] = 1;
                        }
                        $result[$child->nodeName][] = $this->toArray($child);
                    } else {
                        $result[$child->nodeName] = $this->toArray($child);
                    }
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
        foreach ($output['config']['datasource'] as $datasource) {
            $data[$datasource['@attributes']['name']] = [
                'name' => $datasource['@attributes']['name'],
                'label' => $datasource['@attributes']['label'],
                'compositeLabel' => isset($datasource['@attributes']['compositeLabel']) ? $datasource['@attributes']['compositeLabel'] : '',
                'previewElements' => isset($datasource['@attributes']['previewElements']) ? $datasource['@attributes']['previewElements'] : '',
                'dataset' => $datasource['@attributes']['dataset'],
            ];
            $fields = [];
            foreach ($datasource['fields']['field'] as $field) {
                foreach ($field['@attributes'] as $key => $value) {
                    $fields[$field['@attributes']['name']][$key] = $value;
                }
                if (isset($field['@attributes']['source'])) {
                    if (in_array($field['@attributes']['source'], ['lookup', 'option', 'reference'])) {
                        $fields[$field['@attributes']['name']]['reference'] = [
                            'target' => $field['reference']['@attributes']['target'],
                            'targetField' => $field['reference']['@attributes']['targetField'],
                            'referencedField' => $field['reference']['@attributes']['referencedField'],
                            'neededField' => $field['reference']['@attributes']['neededField']
                        ];
                    }
                }

                if (isset($field['constraints']['validate'])) {
                    foreach ($field['constraints']['validate'] as $rule) {
                        $fields[$field['@attributes']['name']]['constraints']['validate'][$rule['@attributes']['name']] =
                            isset($rule['@attribute']['value'])
                                ? $rule['@attribute']['value'] : true;
                    }
                }
                if (isset($field['constraints']['filter'])) {
                    foreach ($field['constraints']['filter'] as $filter) {
                        $fields[$field['@attributes']['name']]['constraints']['filter'][] = [
                            'on' => $filter['@attributes']['on'],
                            'by' => $filter['@attributes']['by'],
                            'value' => $filter['@attributes']['value'],
                        ];
                    }
                }
                if (isset($field['constraints']['readonly'])) {
                    foreach ($field['constraints']['readonly'] as $condition) {
                        $fields[$field['@attributes']['name']]['constraints']['readonly'][] = [
                            'on' => $condition['@attributes']['on'],
                            'value' => $condition['@attributes']['value'],
                        ];
                    }
                }
            }
            $data[$datasource['@attributes']['name']]['fields'] = $fields;
            if (!empty($datasource['references'])) {
                foreach ($datasource['references'] as $reference) {
                    $data[$reference['@attributes']['target']]['children'][$datasource['@attributes']['name']][] = [
                        'targetField' => $reference['@attributes']['targetField'],
                        'referencedField' => $reference['@attributes']['referencedField']
                    ];
                }
            }
        }
        return $data;
    }
}

