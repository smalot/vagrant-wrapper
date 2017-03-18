<?php

namespace Smalot\Vagrant\Wrapper;

use Symfony\Component\Process\Process;

/**
 * Class Vagrant
 * @package Smalot\Vagrant\Wrapper
 */
class Vagrant
{
    use CommandTrait;

    const STATUS_POWEROFF = 'poweroff';

    const STATUS_RUNNING = 'running';

    /**
     * @var null|string
     */
    protected $cwd;

    /**
     * @var array
     */
    protected $env;

    /**
     * Vagrant constructor.
     * @param string $cwd
     * @param array $env
     */
    public function __construct($cwd = null, $env = [])
    {
        $this->cwd = $cwd;
        $this->env = $env;
    }

    /**
     * @param bool $prune
     * @return array
     */
    public function getGlobalStatus($prune = false)
    {
        $command = escapeshellarg(self::$bin).' global-status';
        if ($prune) {
            $command .= ' --prune';
        }

        $process = new Process($command, $this->cwd, $this->env, null, 15);
        $status = [];

        if (!$this->run($process)) {
            $output = $process->getOutput();

            $lines = preg_split('/[\n\r]+/', $output);
            $headers = preg_split('/[\s]+/', trim($lines[0]));
            array_shift($lines); // Remove headers.
            array_shift($lines); // Remove separators.

            foreach ($lines as $line) {
                if (!trim($line)) {
                    break;
                }

                if (preg_match('/^(.*?)\s+(.*?)\s+(.*?)\s+(.*?)\s+(.*?)$/', trim($line), $match)) {
                    array_shift($match);
                    $item = array_combine($headers, $match);
                    $status[$item['id']] = $item;
                }
            }
        }

        return $status;
    }

    /**
     * @return string|false
     */
    public function getStatus()
    {
        $command = escapeshellarg(self::$bin).' status';
        $process = new Process($command, $this->cwd, $this->env, null, 15);

        if (!$this->run($process)) {
            $output = $process->getOutput();

            $lines = preg_split('/[\n\r]+/', $output);
            $content = preg_split('/[\s]+/', trim($lines[1]));

            return $content[1];
        }

        return false;
    }

    /**
     * @param string $provider
     * @param string $provision
     * @return Process
     */
    public function doUp($provider = null, $provision = null)
    {
        $command = escapeshellarg(self::$bin).' up';
        if ($provider) {
            $command .= ' --provider '.escapeshellarg($provider);
        }
        if (is_bool($provision)) {
            if ($provision === true) {
                $command .= ' --provision';
            } else {
                $command .= ' --no-provision';
            }
        }

        $process = new Process($command, $this->cwd, $this->env, null, 360);

        return $process;
    }

    /**
     * @param bool $provision
     * @return bool
     */
    public function doReload($provision = null)
    {
        $command = escapeshellarg(self::$bin).' reload';
        if (!is_bool($provision)) {
            if ($provision === true) {
                $command .= ' --provider';
            } else {
                $command .= ' --no-provider';
            }
        }

        $process = new Process($command, $this->cwd, $this->env, null, 60);

        return !$this->run($process);
    }

    /**
     * @param bool $force
     * @return bool
     */
    public function doHalt($force = false)
    {
        $command = escapeshellarg(self::$bin).' halt';
        if ($force) {
            $command .= ' --force';
        }

        $process = new Process($command, $this->cwd, $this->env, null, 60);

        return !$this->run($process);
    }

    /**
     * @return bool
     */
    public function doSuspend()
    {
        $command = escapeshellarg(self::$bin).' suspend';

        $process = new Process($command, $this->cwd, $this->env, null, 60);

        return !$this->run($process);
    }

    /**
     * @return bool
     */
    public function doResume()
    {
        $command = escapeshellarg(self::$bin).' resume';

        $process = new Process($command, $this->cwd, $this->env, null, 60);

        return !$this->run($process);
    }
}
