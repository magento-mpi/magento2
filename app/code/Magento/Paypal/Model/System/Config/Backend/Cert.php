<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for saving certificate file in case of using certificate based authentication
 */
namespace Magento\Paypal\Model\System\Config\Backend;

class Cert extends \Magento\Core\Model\Config\Value
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Process additional data before save config
     *
     * @return \Magento\Paypal\Model\System\Config\Backend\Cert
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
            \Mage::getModel('Magento\Paypal\Model\Cert')->loadByWebsite($this->getScopeId())->delete();
        }

        if (!isset($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'])) {
            return $this;
        }
        $tmpPath = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
        if ($tmpPath && file_exists($tmpPath)) {
            if (!filesize($tmpPath)) {
                \Mage::throwException(__('The PayPal certificate file is empty.'));
            }
            $this->setValue($_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']);
            $content = $this->_coreData->encrypt(file_get_contents($tmpPath));
            \Mage::getModel('Magento\Paypal\Model\Cert')->loadByWebsite($this->getScopeId())
                ->setContent($content)
                ->save();
        }
        return $this;
    }

    /**
     * Process object after delete data
     *
     * @return \Magento\Paypal\Model\System\Config\Backend\Cert
     */
    protected function _afterDelete()
    {
        \Mage::getModel('Magento\Paypal\Model\Cert')->loadByWebsite($this->getScopeId())->delete();
        return $this;
    }
}
