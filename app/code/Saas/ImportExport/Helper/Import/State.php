<?php
/**
 * Saas import state helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Import_State extends Saas_ImportExport_Helper_StateAbstract
{
    /**
     * {@inheritdoc}
     */
    public function onValidationShutdown()
    {
        $error = error_get_last();
        if ($error && isset($error['type']) && $error['type'] == E_ERROR && $this->isInProgress()) {
            $this->saveTaskAsNotified();
        }
    }
}
