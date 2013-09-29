<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Template db resource
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Email;

class Template extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize email template resource model
     *
     */
    protected function _construct()
    {
        $this->_init('core_email_template', 'template_id');
    }

    /**
     * Load by template code from DB.
     *
     * @param string $templateCode
     * @return array
     */
    public function loadByCode($templateCode)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('template_code = :template_code');
        $result = $this->_getReadAdapter()->fetchRow($select, array('template_code' => $templateCode));

        if (!$result) {
            return array();
        }
        return $result;
    }

    /**
     * Check usage of template code in other templates
     *
     * @param \Magento\Core\Model\Email\Template $template
     * @return boolean
     */
    public function checkCodeUsage(\Magento\Core\Model\Email\Template $template)
    {
        if ($template->getTemplateActual() != 0 || is_null($template->getTemplateActual())) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), 'COUNT(*)')
                ->where('template_code = :template_code');
            $bind = array(
                'template_code' => $template->getTemplateCode()
            );

            $templateId = $template->getId();
            if ($templateId) {
                $select->where('template_id != :template_id');
                $bind['template_id'] = $templateId;
            }

            $result = $this->_getReadAdapter()->fetchOne($select, $bind);
            if ($result) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set template type, added at and modified at time
     *
     * @param \Magento\Core\Model\Email\Template $object
     * @return \Magento\Core\Model\Resource\Email\Template
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->formatDate(true));
        }
        $object->setModifiedAt($this->formatDate(true));
        $object->setTemplateType((int)$object->getTemplateType());

        return parent::_beforeSave($object);
    }

    /**
     * Retrieve config scope and scope id of specified email template by email pathes
     *
     * @param array $paths
     * @param int|string $templateId
     * @return array
     */
    public function getSystemConfigByPathsAndTemplateId($paths, $templateId)
    {
        $orWhere = array();
        $pathesCounter = 1;
        $bind = array();
        foreach ($paths as $path) {
            $pathAlias = 'path_' . $pathesCounter;
            $orWhere[] = 'path = :' . $pathAlias;
            $bind[$pathAlias] = $path;
            $pathesCounter++;
        }
        $bind['template_id'] = $templateId;
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('core_config_data'), array('scope', 'scope_id', 'path'))
            ->where('value LIKE :template_id')
            ->where(join(' OR ', $orWhere));

        return $this->_getReadAdapter()->fetchAll($select, $bind);
    }
}
