<?php
/**
 * Multi-tenant deployment code base service model
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\MultiTenant;

class CodeBase
{
    /**
     * @var \Magento_Shell
     */
    private $_shell;

    /**
     * @var \Zend_Log
     */
    private $_log;

    /**
     * @var string
     */
    private $_deployDir;

    /**
     * @var string
     */
    private $_workingDir;

    /**
     * Set dependencies and basic state parameters
     *
     * @param \Magento_Shell $shell
     * @param \Zend_Log $logger
     * @param string $workingDir
     * @param string $deployDir
     * @throws \Exception
     */
    public function __construct(\Magento_Shell $shell, \Zend_Log $logger, $workingDir, $deployDir)
    {
        $this->_shell = $shell;
        $this->_log = $logger;
        if (!is_dir($workingDir)) {
            throw new \Exception("Working directory does not exist: '{$workingDir}'");
        }
        if (!is_dir($deployDir) || !is_writable($deployDir)) {
            throw new \Exception("Deployment directory does not exist or not writable: '{$deployDir}'");
        }
        $this->_workingDir = $workingDir;
        $this->_deployDir = $deployDir;
    }

    /**
     * Get deployment directory path
     *
     * @return string
     */
    public function getDeployDir()
    {
        return $this->_deployDir;
    }

    /**
     * Delete the deployment directory, re-create it and clone the Git repository from working directory
     */
    public function recreateDeployDir()
    {
        if (is_dir($this->_deployDir)) {
            $this->_rmDir($this->_deployDir);
        }
        $this->_log->log("mkdir('{$this->_deployDir}')", \Zend_Log::INFO);
        mkdir($this->_deployDir);
        $this->_shell->execute('git clone %s %s', array($this->_workingDir, $this->_deployDir));
        $this->override();

    }

    /**
     * Fetch updates from original Git repository
     */
    public function updateDeployDir()
    {
        $this->_ensureRepository();
        $this->_shell->execute('git --work-tree=%s --git-dir=%s reset --hard', array(
            $this->_deployDir, $this->_deployDir . '/.git'
        ));
        $this->_shell->execute('git --work-tree=%s --git-dir=%s pull origin', array(
            $this->_deployDir, $this->_deployDir . '/.git'
        ));
        $this->override();
    }

    /**
     * Delete the specified directory from deployment dir and create it
     *
     * @param string $dir relative to deployment dir
     * @return string absolute path to the affected directory
     */
    public function recreateDir($dir)
    {
        $this->removeDir($dir);
        $targetDir = "{$this->_deployDir}/{$dir}";
        $this->_log->log("mkdir('{$targetDir}')", \Zend_Log::INFO);
        mkdir($targetDir);
        return $targetDir;
    }

    /**
     * Remove a directory fromm deployment dir
     *
     * @param string $dir relative to the deployment dir
     * @throws \Exception
     */
    public function removeDir($dir)
    {
        if (empty($dir)) {
            throw new \Exception('Directory name must be not empty.');
        }
        $targetDir = "{$this->_deployDir}/{$dir}";
        $this->_rmDir($targetDir);
    }

    /**
     * Make code base non-accessible to the end-user
     *
     * @param bool $lock
     */
    public function setLock($lock = true)
    {
        if ($lock) {
            $this->_patchFile('Allow from all', 'Deny from all', "{$this->_deployDir}/.htaccess");
        } else {
            $this->override(); // it will restore and patch .htaccess properly thus opening access back
        }
    }

    /**
     * Impose build-specific files on code base
     *
     * Copy fresh index.build.php into the deployment dir and adjust the .htaccess accordingly
     */
    public function override()
    {
        $this->_ensureRepository();

        // custom entry point
        $from =  "{$this->_workingDir}/dev/build/saas_qa/index.build.php";
        $to = "{$this->_deployDir}/index.build.php";
        $this->_log->log("copy({$from}, {$to})", \Zend_Log::INFO);
        copy($from, $to);

        // .htaccess
        $this->_shell->execute('git --work-tree=%s --git-dir=%s checkout -- .htaccess', array(
            $this->_deployDir, $this->_deployDir . '/.git'
        ));
        $this->_patchFile('index.php', 'index.build.php', "{$this->_deployDir}/.htaccess");
    }

    /**
     * Verify if the repository is in place and create it if it is not
     */
    private function _ensureRepository()
    {
        if (!file_exists("{$this->_deployDir}/.git")) {
            $this->recreateDeployDir();
        }
    }

    /**
     * str_replace() in a file
     *
     * @param string $from
     * @param string $to
     * @param string $file
     */
    private function _patchFile($from, $to, $file)
    {
        $this->_log->log("str_replace('{$from}', '{$to}', {$file})", \Zend_Log::INFO);
        $contents = file_get_contents($file);
        $contents = str_replace($from, $to, $contents);
        file_put_contents($file, $contents, LOCK_EX);
    }

    /**
     * Remove a directory using shell command
     *
     * @param string $dir
     */
    private function _rmDir($dir)
    {
        $isWindows = '\\' == DIRECTORY_SEPARATOR;
        if ($isWindows) {
            // workaround: on Windows sometimes there are random files still remaining
            for ($i = 0; $i < 2; $i++) {
                clearstatcache();
                if (is_dir($dir)) {
                    $this->_shell->execute('rmdir /S /Q %s', array($dir));
                    sleep(1);
                }
            }
        } else {
            $this->_shell->execute('rm -rf %s', array($dir));
        }
    }
}
