<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter template resource model
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Model_Resource_Template extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Date
     *
     * @var Magento_Core_Model_Date
     */
    protected $_date;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Date $date
     */
    public function __construct(
        Magento_Core_Model_Date $date,
        Magento_Core_Model_Resource $resource
    ) {
        parent::__construct($resource);
        $this->_date = $date;
    }

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('newsletter_template', 'template_id');
    }

    /**
     * Load an object by template code
     *
     * @param Magento_Newsletter_Model_Template $object
     * @param string $templateCode
     * @return Magento_Newsletter_Model_Resource_Template
     */
    public function loadByCode(Magento_Newsletter_Model_Template $object, $templateCode)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($templateCode)) {
            $select = $this->_getLoadSelect('template_code', $templateCode, $object)
                ->where('template_actual = :template_actual');
            $data = $read->fetchRow($select, array('template_actual'=>1));

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Check usage of template in queue
     *
     * @param Magento_Newsletter_Model_Template $template
     * @return boolean
     */
    public function checkUsageInQueue(Magento_Newsletter_Model_Template $template)
    {
        if ($template->getTemplateActual() !== 0 && !$template->getIsSystem()) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('newsletter_queue'), new Zend_Db_Expr('COUNT(queue_id)'))
                ->where('template_id = :template_id');

            $countOfQueue = $this->_getReadAdapter()->fetchOne($select, array('template_id'=>$template->getId()));

            return $countOfQueue > 0;
        } elseif ($template->getIsSystem()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check usage of template code in other templates
     *
     * @param Magento_Newsletter_Model_Template $template
     * @return boolean
     */
    public function checkCodeUsage(Magento_Newsletter_Model_Template $template)
    {
        if ($template->getTemplateActual() != 0 || is_null($template->getTemplateActual())) {
            $bind = array(
                'template_id'     => $template->getId(),
                'template_code'   => $template->getTemplateCode(),
                'template_actual' => 1
            );
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), new Zend_Db_Expr('COUNT(template_id)'))
                ->where('template_id != :template_id')
                ->where('template_code = :template_code')
                ->where('template_actual = :template_actual');

            $countOfCodes = $this->_getReadAdapter()->fetchOne($select, $bind);

            return $countOfCodes > 0;
        } else {
            return false;
        }
    }

    /**
     * Perform actions before object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Newsletter_Model_Resource_Template
     * @throws Magento_Core_Exception
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if ($this->checkCodeUsage($object)) {
            throw new Magento_Core_Exception(__('Duplicate template code'));
        }

        if (!$object->hasTemplateActual()) {
            $object->setTemplateActual(1);
        }
        if (!$object->hasAddedAt()) {
            $object->setAddedAt($this->_date->gmtDate());
        }
        $object->setModifiedAt($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }
}
