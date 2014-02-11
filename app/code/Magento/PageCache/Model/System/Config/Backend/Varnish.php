<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\System\Config\Backend;

/**
 * Backend model for processing Varnish settings
 *
 * Class Varnish
 * @package Magento\PageCache\Model\System\Config\Backend
 */
class Varnish extends \Magento\Core\Model\Config\Value
{
    /**
     * @var mixed|string
     */
    protected $defaultValues;

    /**
     * Set default data if empty fields have been left
     *
     * @return $this|\Magento\Core\Model\AbstractModel
     * @throws \Magento\Core\Exception
     */
    protected function _beforeSave()
    {
        $data = $this->_getDefaultValues();
        $currentValue = $this->getValue();
        if(!$currentValue) {
            $replaceValue = isset($data[$this->getField()]) ? $data[$this->getField()] : false;
            if (!$replaceValue) {
                throw new \Magento\Core\Exception(__("Field {$this->getField()} not matched with default field"));
            }
            $this->setValue($replaceValue);
        }
        return $this;
    }

    /**
     * Get Default Config Values
     *
     * @return mixed|string
     */
    protected function _getDefaultValues()
    {
        if (!$this->defaultValues) {
            $this->defaultValues = $this->_config->getValue('system/full_page_cache/default');
        }
        return $this->defaultValues;
    }
}