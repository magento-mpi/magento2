<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Convert container abstract
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Dataflow_Model_Convert_Container_Abstract
    implements Mage_Dataflow_Model_Convert_Container_Interface
{
    protected $_batchParams = array();

    protected $_vars;

    protected $_profile;

    protected $_action;

    protected $_data;

    protected $_position;

    public function getVar($key, $default=null)
    {
        if (!isset($this->_vars[$key]) || (!is_array($this->_vars[$key]) && strlen($this->_vars[$key]) == 0)) {
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

    public function getAction()
    {
        return $this->_action;
    }

    public function setAction(Mage_Dataflow_Model_Convert_Action_Interface $action)
    {
        $this->_action = $action;
        return $this;
    }

    public function getProfile()
    {
        return $this->_profile;
    }

    public function setProfile(Mage_Dataflow_Model_Convert_Profile_Interface $profile)
    {
        $this->_profile = $profile;
        return $this;
    }

    public function getData()
    {
        if (is_null($this->_data) && $this->getProfile()) {
            $this->_data = $this->getProfile()->getContainer()->getData();
        }
        return $this->_data;
    }

    public function setData($data)
    {
        if ($this->getProfile()) {
            $this->getProfile()->getContainer()->setData($data);
        }
        $this->_data = $data;
        return $this;
    }

    public function validateDataString($data=null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }
        if (!is_string($data)) {
            $this->addException("Invalid data type, expecting string.", Mage_Dataflow_Model_Convert_Exception::FATAL);
        }
        return true;
    }

    public function validateDataArray($data=null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }
        if (!is_array($data)) {
            $this->addException("Invalid data type, expecting array.", Mage_Dataflow_Model_Convert_Exception::FATAL);
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
            $this->addException("Invalid data type, expecting 2D grid array.", Mage_Dataflow_Model_Convert_Exception::FATAL);
        }
        return true;
    }

    public function getGridFields($grid)
    {
        $fields = array();
        foreach ($grid as $i=>$row) {
            foreach ($row as $fieldName=>$data) {
                if (!in_array($fieldName, $fields)) {
                    $fields[] = $fieldName;
                }
            }
        }
        return $fields;
    }

    public function addException($error, $level=null)
    {
        $e = new Mage_Dataflow_Model_Convert_Exception($error);
        $e->setLevel(!is_null($level) ? $level : Mage_Dataflow_Model_Convert_Exception::NOTICE);
        $e->setContainer($this);
        $e->setPosition($this->getPosition());

        if ($this->getProfile()) {
            $this->getProfile()->addException($e);
        }

        return $e;
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

    public function setBatchParams($data)
    {
        if (is_array($data)) {
            $this->_batchParams = $data;
        }
        return $this;
    }

    public function getBatchParams($key = null)
    {
        if (!empty($key)) {
            return isset($this->_batchParams[$key]) ? $this->_batchParams[$key] : null;
        }
        return $this->_batchParams;
    }
}
