<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Install\Test\Block;

use Mtf\Client\Element;

/**
 * Is used to replace locator on radio button.
 *
 * @api
 */
class ConfigurationForm extends \Mtf\Block\Form
{
    /**
     * Fixture mapping.
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $mapping = [];
        $mappingFields = ($parent !== null) ? $parent : $this->mapping;
        $data = ($this->mappingMode || null === $fields) ? $mappingFields : $fields;
        foreach ($data as $key => $value) {
            if (isset($value['value'])) {
                $value = $value['value'];
            }
            if (!$this->mappingMode && is_array($value) && null !== $fields
                && isset($mappingFields[$key]['composite'])
            ) {
                $mapping[$key] = $this->dataMapping($value, $mappingFields[$key]);
            } else {
                $mapping[$key]['selector'] = isset($mappingFields[$key]['selector'])
                    ? $mappingFields[$key]['selector']
                    : (($this->wrapper != '') ? "[name='{$this->wrapper}" . "[{$key}]']" : "[name={$key}]");
                if (isset($fields[$key]) && strpos($mapping[$key]['selector'], '%s') !== false) {
                    $mapping[$key]['selector'] = sprintf($mapping[$key]['selector'], $fields[$key]);
                    $fields[$key] = "Yes";
                    $value = "Yes";
                }
                $mapping[$key]['strategy'] = isset($mappingFields[$key]['strategy'])
                    ? $mappingFields[$key]['strategy']
                    : Element\Locator::SELECTOR_CSS;
                $mapping[$key]['input'] = isset($mappingFields[$key]['input'])
                    ? $mappingFields[$key]['input']
                    : null;
                $mapping[$key]['class'] = isset($mappingFields[$key]['class'])
                    ? $mappingFields[$key]['class']
                    : null;
                $mapping[$key]['value'] = $this->mappingMode
                    ? (isset($fields[$key]['value']) ? $fields[$key]['value'] : $fields[$key])
                    : $value;
            }
        }

        return $mapping;
    }
}
