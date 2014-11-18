<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

require __DIR__ . '/../app/autoload.php';
\Zend\Mvc\Application::init(require __DIR__ . '/config/application.config.php')->run();
