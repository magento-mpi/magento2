<?php
/**
 * Export finished message class
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_ImportExport_Model_System_Message_ExportFinished extends Saas_ImportExport_Model_System_Message_Abstract
{
    /**
     * Message Identity
     */
    const MESSAGE_IDENTITY = 'EXPORT_ENTITY';

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return $this->_stateHelper->__('The Export task has been finished.');
    }

    /**
     * @inheritdoc
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }
}
