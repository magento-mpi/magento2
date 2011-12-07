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
 * Convert STDIO adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert_Adapter_Std extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{

    public function load()
    {
        $data = '';
        $stdin = fopen('php://STDIN', 'r');
        while ($text = fread($stdin, 1024)) {
            $data .= $text;
        }
        $this->setData($data);
        return $this;
    }

    public function save()
    {
        echo $this->getData();
        return $this;
    }

}
