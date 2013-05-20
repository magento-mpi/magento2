<?php
/**
 * Import finished message class
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_ImportExport_Model_System_Message_ImportFinished extends Saas_ImportExport_Model_System_Message_Abstract
{
    /**
     * Message Identity
     */
    const MESSAGE_IDENTITY = 'IMPORT_ENTITY';

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getText()
    {
        return $this->_stateHelper->__('The Import task has been finished.');
    }

    /**
     * @inheritdoc
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }
}
