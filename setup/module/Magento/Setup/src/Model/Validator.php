<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Setup\Module\Setup\ConfigMapper;
use Magento\Setup\Controller\ConsoleController;

class Validator
{
    private $validationMessages = [];

    /**
     * Get validation messages
     *
     * @return string
     */
    public function getValidationMessages()
    {
        $message = '';
        foreach ($this->validationMessages as $key => $value) {
            $message .= "{$key}: {$value}" . PHP_EOL;
        }
        return $message;
    }

    /**
     * Check for any missing parameters
     *
     * @param array $expectedParams
     * @param array $actualParams
     * @return array
     */
    public function checkMissingParameter($expectedParams, $actualParams)
    {
        $missingParams = array_diff(array_keys($expectedParams), array_keys($actualParams));
        foreach ($missingParams as $key => $missingParam) {
            /* disregard if optional parameter */
            if (!$expectedParams[$missingParam]['required']) {
                unset($missingParams[$key]);
            }
        }
        return $missingParams;
    }

    /**
     * Check for any extra parameters
     *
     * @param array $expectedParams
     * @param array $actualParams
     * @return array
     */
    public function checkExtraParameter($expectedParams, $actualParams)
    {
        $extraParams = array_diff(array_keys($actualParams), array_keys($expectedParams));
        return $extraParams;
    }

    /**
     * Checks for parameters that are missing values
     *
     * @param $expectedParams
     * @param $actualParams
     * @return array
     */
    public function checkMissingValue($expectedParams, $actualParams)
    {
        $missingValues = [];
        foreach ($actualParams as $param => $value) {
            if (isset($expectedParams[$param])) {
                if ($value === '' && $expectedParams[$param]['hasValue']) {
                    $missingValues[] = $param;
                }
            }
        }
        return $missingValues;
    }

    /**
     * Checks for parameters that do not need values
     *
     * @param $expectedParams
     * @param $actualParams
     * @return array
     */
    public function checkExtraValue($expectedParams, $actualParams)
    {
        $extraValues = [];
        foreach ($actualParams as $param => $value) {
            if (isset($expectedParams[$param])) {
                if ($value !== '' && !$expectedParams[$param]['hasValue']) {
                    $extraValues[] = $param;
                }
            }
        }
        return $extraValues;
    }
    /**
     * Validate parameters according to action
     *
     * @param string $action
     * @param array $data
     * @return bool
     */
    public function validate($action, array $data)
    {
        switch ($action)
        {
            case ConsoleController::CMD_INSTALL:
                return $this->validateInstall($data);
            case ConsoleController::CMD_INSTALL_ADMIN_USER:
                return $this->validateAdmin($data);
            case ConsoleController::CMD_INSTALL_USER_CONFIG:
                return $this->validateUserConfig($data);
            case ConsoleController::CMD_INSTALL_CONFIG:
                return $this->validateDeploymentConfig($data);
            default:
                return true;
        }
    }

    /**
     * Validate parameter values of installation tool
     *
     * @param array $data
     * @return bool
     */
    private function validateInstall(array $data)
    {
        $deploymentValid = $this->validateDeploymentConfig($data);
        $adminValid = $this->validateAdmin($data);
        $userValid = $this->validateUserConfig($data);
        return $deploymentValid && $adminValid && $userValid;
    }

    /**
     * Validate parameter values of deployment configuration tool
     *
     * @param array $data
     * @return bool
     */
    private function validateDeploymentConfig(array $data)
    {
        $pass = true;
        if (isset($data[ConfigMapper::KEY_BACKEND_FRONTNAME]) &&
            !preg_match('/^[a-zA-Z0-9_]+$/', $data[ConfigMapper::KEY_BACKEND_FRONTNAME])
        ) {
            $pass = false;
            $this->validationMessages[ConfigMapper::KEY_BACKEND_FRONTNAME] =
                'Please use alphanumeric characters and underscore. ' .
                "Current: {$data[ConfigMapper::KEY_BACKEND_FRONTNAME]}";
        }
        if (isset($data[ConfigMapper::KEY_SESSION_SAVE]) &&
            $data[ConfigMapper::KEY_SESSION_SAVE] !== 'files' &&
            $data[ConfigMapper::KEY_SESSION_SAVE] !== 'db'
        ) {
            $pass = false;
            $this->validationMessages[ConfigMapper::KEY_SESSION_SAVE] =
                "Please use 'files' or 'db'. Current: {$data[ConfigMapper::KEY_SESSION_SAVE]}";
        }
        if (isset($data[ConfigMapper::KEY_ENCRYPTION_KEY]) &&
            !$data[ConfigMapper::KEY_ENCRYPTION_KEY]
        ) {
            $pass = false;
            $this->validationMessages[ConfigMapper::KEY_ENCRYPTION_KEY] =
                "Please enter a valid encryption key. Current: {$data[ConfigMapper::KEY_ENCRYPTION_KEY]}";
        }

        return $pass;
    }

    /**
     * Validate parameter values of user configuration tool
     *
     * @param array $data
     * @return bool
     */
    private function validateUserConfig(array $data)
    {
        $pass = true;
        // check URL
        if (isset($data[UserConfigurationDataMapper::KEY_BASE_URL]) &&
            !$this->validateUrl($data[UserConfigurationDataMapper::KEY_BASE_URL])) {
            $pass = false;
            $this->validationMessages[UserConfigurationDataMapper::KEY_BASE_URL] =
                "Please enter a valid base url. Current: {$data[UserConfigurationDataMapper::KEY_BASE_URL]}";
        }
        if (isset($data[UserConfigurationDataMapper::KEY_BASE_URL_SECURE]) &&
            !$this->validateUrl($data[UserConfigurationDataMapper::KEY_BASE_URL_SECURE], true)) {
            $pass = false;
            $this->validationMessages[UserConfigurationDataMapper::KEY_BASE_URL_SECURE] =
                'Please enter a valid secure base url. ' .
                "Current: {$data[UserConfigurationDataMapper::KEY_BASE_URL_SECURE]}";
        }

        // check 0/1 options
        $flags = [];
        if (isset($data[UserConfigurationDataMapper::KEY_USE_SEF_URL])) {
            $flags[UserConfigurationDataMapper::KEY_USE_SEF_URL] = $data[UserConfigurationDataMapper::KEY_USE_SEF_URL];
        }
        if (isset($data[UserConfigurationDataMapper::KEY_IS_SECURE])) {
            $flags[UserConfigurationDataMapper::KEY_IS_SECURE] = $data[UserConfigurationDataMapper::KEY_IS_SECURE];
        }
        if (isset($data[UserConfigurationDataMapper::KEY_IS_SECURE_ADMIN])) {
            $flags[UserConfigurationDataMapper::KEY_IS_SECURE_ADMIN] =
                $data[UserConfigurationDataMapper::KEY_IS_SECURE_ADMIN];
        }
        if (isset($data[UserConfigurationDataMapper::KEY_ADMIN_USE_SECURITY_KEY])) {
            $flags[UserConfigurationDataMapper::KEY_ADMIN_USE_SECURITY_KEY] =
                $data[UserConfigurationDataMapper::KEY_ADMIN_USE_SECURITY_KEY];
        }
        if (!$this->validateOneZero($flags)) {
            $pass = false;
        }

        // check language, currency and timezone
        $options = new Lists(new \Zend_Locale());
        if (isset($data[UserConfigurationDataMapper::KEY_LANGUAGE])) {
            if (!isset($options->getLocaleList()[$data[UserConfigurationDataMapper::KEY_LANGUAGE]])) {
                $pass = false;
                $this->validationMessages[UserConfigurationDataMapper::KEY_LANGUAGE] =
                    'Please use a valid language. ' .
                    "Current: {$data[UserConfigurationDataMapper::KEY_LANGUAGE]}";
            }
        }

        if (isset($data[UserConfigurationDataMapper::KEY_CURRENCY])) {
            if (!isset($options->getCurrencyList()[$data[UserConfigurationDataMapper::KEY_CURRENCY]])) {
                $pass = false;
                $this->validationMessages[UserConfigurationDataMapper::KEY_CURRENCY] =
                    'Please use a valid currency. ' .
                    "Current: {$data[UserConfigurationDataMapper::KEY_CURRENCY]}";
            }
        }

        if (isset($data[UserConfigurationDataMapper::KEY_TIMEZONE])) {
            if (!isset($options->getTimezoneList()[$data[UserConfigurationDataMapper::KEY_TIMEZONE]])) {
                $pass = false;
                $this->validationMessages[UserConfigurationDataMapper::KEY_TIMEZONE] =
                    'Please use a valid timezone. ' .
                    "Current: {$data[UserConfigurationDataMapper::KEY_TIMEZONE]}";
            }
        }

        return $pass;
    }

    /**
     * Validate parameter values of admin user setup tool
     *
     * @param array $data
     * @return bool
     */
    private function validateAdmin(array $data)
    {
        $pass = true;
        if (isset($data[AdminAccount::KEY_EMAIL]) &&
            !$this->validateEmail($data[AdminAccount::KEY_EMAIL])
        ) {
            $pass = false;
            $this->validationMessages[AdminAccount::KEY_EMAIL] =
                'Please enter a valid email address. ' .
                "Current: {$data[AdminAccount::KEY_EMAIL]}";
        }
        if (isset($data[AdminAccount::KEY_PASSWORD]) &&
            strlen($data[AdminAccount::KEY_PASSWORD]) < 7
        ) {
            $pass = false;
            $this->validationMessages[AdminAccount::KEY_PASSWORD] = 'Password must be at least 7 characters long.';
        }

        return $pass;

    }

    /**
     * Validate email
     *
     * @param $email
     * @return bool
     */
    private function validateEmail($email)
    {
        $validator = new \Zend\Validator\EmailAddress();
        return $validator->isValid($email);
    }

    /**
     * Validate URL
     *
     * @param string $url
     * @param bool $secure
     * @return bool
     */
    private function validateUrl($url, $secure = false)
    {
        $validator = new \Zend\Validator\Uri();
        if ($validator->isValid($url)) {
            if ($secure) {
                return strpos($url, 'https://') !== false;
            }
            else {
                return strpos($url, 'http://') !== false;
            }
        }
        return false;
    }

    /**
     * Validate if all flags are of 0/1 option
     *
     * @param array $flags
     * @return bool
     */
    private function validateOneZero(array $flags = [])
    {
        $wrongOptionMessage = 'Please enter a valid option (0/1). ';
        $pass = true;
        foreach ($flags as $key => $flag) {
            if ($flag !== '0' && $flag !== '1') {
                $pass = false;
                $this->validationMessages[$key] = "{$wrongOptionMessage} Current: {$flag}";
            }
        }
        return $pass;
    }
}
