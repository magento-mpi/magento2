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
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Dataflow_Model_Convert_Profile_Abstract
    implements Mage_Dataflow_Model_Convert_Profile_Interface
{

    protected $_actions;

    protected $_containers;

    protected $_exceptions = array();

    protected $_dryRun;

    protected $_actionDefaultClass = 'Mage_Dataflow_Model_Convert_Action';

    protected $_containerCollectionDefaultClass = 'Mage_Dataflow_Model_Convert_Container_Collection';

    protected $_dataflow_profile = null;

    public function addAction(Mage_Dataflow_Model_Convert_Action_Interface $action=null)
    {
        if (is_null($action)) {
            $action = new $this->_actionDefaultClass();
        }
        $this->_actions[] = $action;
        $action->setProfile($this);
        return $action;
    }

    public function setContainers(Mage_Dataflow_Model_Convert_Container_Collection $containers)
    {
        $this->_containers = $containers;
        return $this;
    }

    public function getContainers()
    {
        if (!$this->_containers) {
            $this->_containers = new $this->_containerCollectionDefaultClass();
        }
        return $this->_containers;
    }

    public function getContainer($name=null)
    {
        if (is_null($name)) {
            $name = '_default';
        }
        return $this->getContainers()->getItem($name);
    }

    public function addContainer($name, Mage_Dataflow_Model_Convert_Container_Interface $container)
    {
        $container = $this->getContainers()->addItem($name, $container);
        $container->setProfile($this);
        return $container;
    }

    public function getExceptions()
    {
        return $this->_exceptions;
    }

    public function getDryRun()
    {
        return $this->_dryRun;
    }

    public function setDryRun($flag)
    {
        $this->_dryRun = $flag;
        return $this;
    }

    public function addException(Mage_Dataflow_Model_Convert_Exception $e)
    {
        $this->_exceptions[] = $e;
        return $this;
    }

    public function importXml(Varien_Simplexml_Element $profileNode)
    {
        foreach ($profileNode->action as $actionNode) {
            $action = $profile->addAction();
            $action->importXml($actionNode);
        }

        return $this;
    }

    public function run()
    {
//        print '<pre>';
//        print_r($this->_dataflow_profile);
//        print '</pre>';

        if (!$this->_actions) {
            $e = new Mage_Dataflow_Model_Convert_Exception("Could not find any actions for this profile");
            $e->setLevel(Mage_Dataflow_Model_Convert_Exception::FATAL);
            $this->addException($e);
            return;
        }

        foreach ($this->_actions as $action) {
            /* @var $action Mage_Dataflow_Model_Convert_Action */
            try {
                $action->run();
            }
            catch (Exception $e) {
                $dfe = new Mage_Dataflow_Model_Convert_Exception($e->getMessage());
                $dfe->setLevel(Mage_Dataflow_Model_Convert_Exception::FATAL);
                $this->addException($dfe);
                return ;
            }
        }
        return $this;
    }

    function setDataflowProfile($profile) {
        if (is_array($profile)) {
            $this->_dataflow_profile = $profile;
        }
        return $this;
    }

    function getDataflowProfile()
    {
        return $this->_dataflow_profile;
    }
}
