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

use Magento\Convert\ConvertException;

abstract class AbstractContainer
{
    /**
     * @var array
     */
    protected $_vars;

    /**
     * @var array
     */
    protected $_data;

    /**
     * @var int
     */
    protected $_position;

    /**
     * @param string $key
     * @param string $default
     * @return array
     */
    public function getVar($key, $default=null)
    {
        if (!isset($this->_vars[$key])) {
            return $default;
        }
        return $this->_vars[$key];
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * @param array|string $key
     * @param string|null $value
     * @return $this
     */
    public function setVar($key, $value=null)
    {
        if (is_array($key) && is_null($value)) {
            $this->_vars = $key;
        } else {
            $this->_vars[$key] = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @param array|null $data
     * @return true
     */
    public function validateDataString($data=null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }
        if (!is_string($data)) {
            $this->addException("Invalid data type, expecting string.", ConvertException::FATAL);
        }
        return true;
    }

    /**
     * @param array $data
     * @return true
     */
    public function validateDataGrid($data=null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }
        if (!is_array($data) || !is_array(current($data))) {
            if (count($data)==0) {
                return true;
            }
            $this->addException(
                "Invalid data type, expecting 2D grid array.", ConvertException::FATAL);
        }
        return true;
    }

    /**
     * @param array $grid
     * @return array
     */
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

    /**
     * @param string $error
     * @param string|null $level
     * @return ConvertException
     */
    public function addException($error, $level=null)
    {
        $exception = new ConvertException($error);
        $exception->setLevel(!is_null($level) ? $level : ConvertException::NOTICE);
        $exception->setContainer($this);
        $exception->setPosition($this->getPosition());

        return $exception;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }
}
