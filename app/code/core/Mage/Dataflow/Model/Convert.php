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
 * Dataflow Convert factory
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert
{

    static public function convert($class, $method, $data, array $vars=array())
    {
        if (is_string($class)) {
            $class = new $class();
        }
        $action = new Mage_Dataflow_Model_Convert_Action();
        $action->setParam('method', $method)->setParam('class', $class);

        $container = $action->getContainer();
        $container->setData($data)->setVars($vars);

        $action->run();
        return $action->getData();
    }

}
