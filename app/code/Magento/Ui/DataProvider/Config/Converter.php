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
     * @param string $source
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
        foreach ($output['config']['dataSource'] as $dataSource) {
            $data[$dataSource['@attributes']['name']] = [
                'name' => $dataSource['@attributes']['name'],
                'label' => $dataSource['@attributes']['label'],
                'dataSet' => $dataSource['@attributes']['dataSet'],
            ];
            $fields = [];
            foreach ($dataSource['fields']['field'] as $field) {
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
                        $filterValues['on'] = isset($filter['@attributes']['on']) ? $filter['@attributes']['on'] : null;
                        $filterValues['by'] = isset($filter['@attributes']['by']) ? $filter['@attributes']['by'] : null;
                        $filterValues['value'] = isset($filter['@attributes']['value'])
                            ? $filter['@attributes']['value'] : null;
                        $fields[$field['@attributes']['name']]['constraints']['filter'][] = $filterValues;
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
            $data[$dataSource['@attributes']['name']]['fields'] = $fields;
            if (!empty($dataSource['references'])) {
                foreach ($dataSource['references'] as $reference) {
                    $data[$reference['@attributes']['target']]['children'][$dataSource['@attributes']['name']][] = [
                        'targetField' => $reference['@attributes']['targetField'],
                        'referencedField' => $reference['@attributes']['referencedField']
                    ];
                }
            }
        }
        return $data;
    }
}
