<?php
/**
 * Helper for errors processing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

use Magento\App\State;
use Magento\Exception\AbstractAggregateException;
use Magento\Exception\AuthenticationException;
use Magento\Exception\AuthorizationException;
use Magento\Exception\LocalizedException;
use Magento\Exception\NoSuchEntityException;
use Magento\Webapi\Exception as WebapiException;

class ErrorProcessor
{
    const DEFAULT_SHUTDOWN_FUNCTION = 'apiShutdownFunction';

    const DEFAULT_ERROR_HTTP_CODE = 500;

    const DEFAULT_RESPONSE_CHARSET = 'UTF-8';

    /**#@+
     * Error data representation formats.
     */
    const DATA_FORMAT_JSON = 'json';

    const DATA_FORMAT_XML = 'xml';

    /**#@-*/

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * Filesystem instance
     *
     * @var \Magento\App\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Filesystem\Directory\Write
     */
    protected $directoryWrite;

    /**
     * @param \Magento\Core\Helper\Data $helper
     * @param \Magento\App\State $appState
     * @param \Magento\Logger $logger
     * @param \Magento\App\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Core\Helper\Data $helper,
        \Magento\App\State $appState,
        \Magento\Logger $logger,
        \Magento\App\Filesystem $filesystem
    ) {
        $this->_coreHelper = $helper;
        $this->_appState = $appState;
        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
        $this->directoryWrite = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->registerShutdownFunction();
    }

    /**
     * Mask actual exception for security reasons in case when it should not be exposed to API clients.
     *
     * Convert any exception into \Magento\Webapi\Exception.
     *
     * @param \Exception $exception Exception to convert to a WebAPI exception
     *
     * @return WebapiException
     */
    public function maskException(\Exception $exception)
    {
        /** Log information about actual exception. */
        // TODO: MAGETWO-21077 $this->_logException($exception);

        $stackTrace = ($this->_appState->getMode() === State::MODE_DEVELOPER) ?
            $stackTrace = $exception->getTrace() : null;

        if ($exception instanceof LocalizedException) {
            // Map HTTP codes for LocalizedExceptions according to exception type
            if ($exception instanceof NoSuchEntityException) {
                $httpCode = WebapiException::HTTP_NOT_FOUND;
            } elseif (($exception instanceof AuthorizationException) || ($exception instanceof AuthenticationException)) {
                $httpCode = WebapiException::HTTP_UNAUTHORIZED;
            } else {
                // Input, Expired, InvalidState exceptions will fall to here
                $httpCode = WebapiException::HTTP_BAD_REQUEST;
            }

            $errors = null;
            if ($exception instanceof AbstractAggregateException) {
                if ($exception->wasErrorAdded()) {
                    $errors = $exception->getErrors();
                }
            }

            $maskedException = new WebapiException(
                $exception->getRawMessage(),
                $exception->getCode(),
                $httpCode,
                $exception->getParameters(),
                get_class($exception),
                $errors, 
                $stackTrace
            );

        } else if ($exception instanceof WebapiException) {
            $maskedException = $exception;
        } else {
            $maskedException = new WebapiException(
                $exception->getMessage(),
                $exception->getCode(),
                WebapiException::HTTP_INTERNAL_ERROR,
                [],
                null,
                '',
                $stackTrace
            );
        }
        return $maskedException;
    }

    /**
     * Process API exception.
     *
     * Create report if not in developer mode and render error to send correct API response.
     *
     * @param \Exception $exception
     * @param int $httpCode
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function renderException(\Exception $exception, $httpCode = self::DEFAULT_ERROR_HTTP_CODE)
    {
        if ($this->_appState->getMode() == State::MODE_DEVELOPER || $exception instanceof \Magento\Webapi\Exception) {
            $this->render($exception->getMessage(), $exception->getTraceAsString(), $httpCode);
        } else {
            $reportId = $this->_logException($exception);
            $this->render(
                __('Internal Error. Details are available in Magento log file. Report ID: %1', $reportId),
                'Trace is not available.',
                $httpCode
            );
        }
        exit;
    }

    /**
     * Log information about exception to exception log.
     *
     * @param \Exception $exception
     * @return string $reportId
     */
    protected function _logException(\Exception $exception)
    {
        $exceptionClass = get_class($exception);
        $reportId = uniqid("webapi-");
        $exceptionForLog = new $exceptionClass(
            /** Trace is added separately by logException. */
            "Report ID: {$reportId}; Message: {$exception->getMessage()}",
            $exception->getCode()
        );
        $this->_logger->logException($exceptionForLog);
        return $reportId;
    }

    /**
     * Render error according to mime type.
     *
     * @param string $errorMessage
     * @param string $trace
     * @param int $httpCode
     * @return void
     */
    public function render(
        $errorMessage,
        $trace = 'Trace is not available.',
        $httpCode = self::DEFAULT_ERROR_HTTP_CODE
    ) {
        if (isset($_SERVER['HTTP_ACCEPT']) && strstr($_SERVER['HTTP_ACCEPT'], 'xml')) {
            $output = $this->_formatError($errorMessage, $trace, $httpCode, self::DATA_FORMAT_XML);
            $mimeType = 'application/xml';
        } else {
            /** Default format is JSON */
            $output = $this->_formatError($errorMessage, $trace, $httpCode, self::DATA_FORMAT_JSON);
            $mimeType = 'application/json';
        }
        if (!headers_sent()) {
            header('HTTP/1.1 ' . ($httpCode ? $httpCode : self::DEFAULT_ERROR_HTTP_CODE));
            header('Content-Type: ' . $mimeType . '; charset=' . self::DEFAULT_RESPONSE_CHARSET);
        }
        echo $output;
    }

    /**
     * Format error data according to required format.
     *
     * @param string $errorMessage
     * @param string $trace
     * @param int $httpCode
     * @param string $format
     * @return array|string
     */
    protected function _formatError($errorMessage, $trace, $httpCode, $format)
    {
        $errorData = array();
        $message = array('code' => $httpCode, 'message' => $errorMessage);
        $isDeveloperMode = $this->_appState->getMode() == State::MODE_DEVELOPER;
        if ($isDeveloperMode) {
            $message['trace'] = $trace;
        }
        $errorData['messages']['error'][] = $message;
        switch ($format) {
            case self::DATA_FORMAT_JSON:
                $errorData = $this->_coreHelper->jsonEncode($errorData);
                break;
            case self::DATA_FORMAT_XML:
                $errorData = '<?xml version="1.0"?>'
                    . '<error>'
                    . '<messages>'
                    . '<error>'
                    . '<data_item>'
                    . '<code>' . $httpCode . '</code>'
                    . '<message><![CDATA[' . $errorMessage . ']]></message>'
                    . ($isDeveloperMode ? '<trace><![CDATA[' . $trace . ']]></trace>' : '')
                    . '</data_item>'
                    . '</error>'
                    . '</messages>'
                    . '</error>';
                break;
        }
        return $errorData;
    }

    /**
     * Declare web API-specific shutdown function.
     *
     * @return $this
     */
    public function registerShutdownFunction()
    {
        register_shutdown_function(array($this, self::DEFAULT_SHUTDOWN_FUNCTION));
        return $this;
    }

    /**
     * Function to catch errors, that has not been caught by the user error dispatcher function.
     *
     * @return void
     */
    public function apiShutdownFunction()
    {
        $fatalErrorFlag = E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR;
        $error = error_get_last();
        if ($error && $error['type'] & $fatalErrorFlag) {
            $errorMessage = "Fatal Error: '{$error['message']}' in '{$error['file']}' on line {$error['line']}";
            $reportId = $this->_saveFatalErrorReport($errorMessage);
            if ($this->_appState->getMode() == State::MODE_DEVELOPER) {
                $this->render($errorMessage);
            } else {
                $this->render(__('Server internal error. See details in report api/%1', $reportId));
            }
        }
    }

    /**
     * Log information about fatal error.
     *
     * @param string $reportData
     * @return string
     */
    protected function _saveFatalErrorReport($reportData)
    {
        $this->directoryWrite->create('report/api');
        $reportId = abs(intval(microtime(true) * rand(100, 1000)));
        $this->directoryWrite->writeFile('report/api/' . $reportId, serialize($reportData));
        return $reportId;
    }
}
