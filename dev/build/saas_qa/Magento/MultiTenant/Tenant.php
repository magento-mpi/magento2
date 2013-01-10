<?php
/**
 * Multi-tenant deployment tenant domain model
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\MultiTenant;

class Tenant
{
    /**
     * @var string
     */
    private $_id;

    /**
     * @var string
     */
    private $_url;

    /**
     * Set/validate data
     *
     * @param string $identifier
     * @param string $urlPattern
     * @throws \Exception
     */
    public function __construct($identifier, $urlPattern)
    {
        if (!preg_match('/^[a-z0-9]+$/', $identifier)) {
            throw new \Exception("Invalid tenant ID: '{$identifier}'");
        }
        $this->_id = $identifier;
        if (!preg_match('/^https?:\/\/\*\.tenant\..+$/', $urlPattern)) {
            throw new \Exception("Invalid URL pattern: '{$urlPattern}'");
        }
        $this->_url = str_replace('*', $this->_id, $urlPattern);
    }

    /**
     * ID getter
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * DB name getter
     *
     * @return string
     */
    public function getDbName()
    {
        return sprintf('saas_qa_tenant_%s', $this->_id);
    }

    /**
     * URL getter
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * local.xml (customized) filename getter
     *
     * @return string
     */
    public function getLocalXmlFilename()
    {
        return sprintf('local.%s.xml', $this->_id);
    }

    /**
     * Var directory name getter
     *
     * @return string
     */
    public function getVarDirName()
    {
        return 'var.' . $this->_id;
    }

    /**
     * Media directory name getter
     *
     * @return string
     */
    public function getMediaDirName()
    {
        return 'media.' . $this->_id;
    }
}
