<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config element model
 */
namespace Magento\App\Config;

class Element extends \Magento\Simplexml\Element
{
    /**
     * Enter description here...
     *
     * @param string $var
     * @param boolean $value
     * @return boolean
     */
    public function is($var, $value = true)
    {
        $flag = $this->{$var};

        if ($value === true) {
            $flag = strtolower((string)$flag);
            if (!empty($flag) && 'false' !== $flag && 'off' !== $flag) {
                return true;
            } else {
                return false;
            }
        }

        return !empty($flag) && 0 === strcasecmp($value, (string)$flag);
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getClassName()
    {
        if ($this->class) {
            $model = (string)$this->class;
        } elseif ($this->model) {
            $model = (string)$this->model;
        } else {
            return false;
        }
        return $model;
    }
}
