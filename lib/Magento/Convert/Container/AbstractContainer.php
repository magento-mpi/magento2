<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Convert
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Convert container abstract
 *
 * @category   Magento
 * @package    Magento_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Convert\Container;

abstract class AbstractContainer
{
    protected $_vars;
    protected $_data;
    protected $_position;

    public function getVar($key, $default=null)
    {
        if (!isset($this->_vars[$key])) {
            return $default;
        }
        return $this->_vars[$key];
    }

    public function getVars()
    {
        return $this->_vars;
    }

    public function setVar($key, $value=null)
    {
        if (is_array($key) && is_null($value)) {
            $this->_vars = $key;
        } else {
            $this->_vars[$key] = $value;
        }
        return $this;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    public function validateDataString($data=null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }
        if (!is_string($data)) {
            $this->addException("Invalid data type, expecting string.", \Magento\Convert\ConvertException::FATAL);
        }
        return true;
    }

    public function validateDataGrid($data=null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }
        if (!is_array($data) || !is_array(current($data))) {
            if (count($data)==0) {
                return true;
            }
            $this->addException("Invalid data type, expecting 2D grid array.", \Magento\Convert\ConvertException::FATAL);
        }
        return true;
    }

    public function getGridFields($grid)
    {
        $fields = array();
        foreach ($grid as $row) {
            foreach (array_keys($row) as $fieldName) {
                if (!in_array($fieldName, $fields)) {
                    $fields[] = $fieldName;
                }
            }
        }
        return $fields;
    }

    public function addException($error, $level=null)
    {
        $exception = new \Magento\Convert\ConvertException($error);
        $exception->setLevel(!is_null($level) ? $level : \Magento\Convert\ConvertException::NOTICE);
        $exception->setContainer($this);
        $exception->setPosition($this->getPosition());

        return $exception;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }
}
