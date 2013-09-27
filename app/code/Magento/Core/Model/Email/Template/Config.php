<?php
/**
 * High-level interface for email templates data that hides format from the client code
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Email\Template;

class Config
{
    /**
     * @var \Magento\Core\Model\Email\Template\Config\Data
     */
    protected $_dataStorage;

    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_moduleReader;

    /**
     * @param \Magento\Core\Model\Email\Template\Config\Data $dataStorage
     * @param \Magento\Core\Model\Config\Modules\Reader $moduleReader
     */
    public function __construct(
        \Magento\Core\Model\Email\Template\Config\Data $dataStorage,
        \Magento\Core\Model\Config\Modules\Reader $moduleReader
    ) {
        $this->_dataStorage = $dataStorage;
        $this->_moduleReader = $moduleReader;
    }

    /**
     * Retrieve unique identifiers of all available email templates
     *
     * @return string[]
     */
    public function getAvailableTemplates()
    {
        return array_keys($this->_dataStorage->get());
    }

    /**
     * Retrieve translated label of an email template
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateLabel($templateId)
    {
        return __($this->_getInfo($templateId, 'label'));
    }

    /**
     * Retrieve type of an email template
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateType($templateId)
    {
        return $this->_getInfo($templateId, 'type');
    }

    /**
     * Retrieve fully-qualified name of a module an email template belongs to
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateModule($templateId)
    {
        return $this->_getInfo($templateId, 'module');
    }

    /**
     * Retrieve full path to an email template file
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateFilename($templateId)
    {
        $module = $this->getTemplateModule($templateId);
        $file = $this->_getInfo($templateId, 'file');
        return $this->_moduleReader->getModuleDir('view', $module) . '/email/' . $file;
    }

    /**
     * Retrieve value of a field of an email template
     *
     * @param string $templateId Name of an email template
     * @param string $fieldName Name of a field value of which to return
     * @return string
     * @throws UnexpectedValueException
     */
    protected function _getInfo($templateId, $fieldName)
    {
        $data = $this->_dataStorage->get();
        if (!isset($data[$templateId])) {
            throw new \UnexpectedValueException("Email template '$templateId' is not defined.");
        }
        if (!isset($data[$templateId][$fieldName])) {
            throw new \UnexpectedValueException("Field '$fieldName' is not defined for email template '$templateId'.");
        }
        return $data[$templateId][$fieldName];
    }
}
