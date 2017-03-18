<?php

namespace Smalot\Vagrant\Wrapper;

use Symfony\Component\Process\Process;

/**
 * Class Snapshot
 * @package Smalot\Vagrant\Wrapper
 */
class Snapshot
{
    use CommandTrait;

    /**
     * @var null|string
     */
    protected $cwd;

    /**
     * Vagrant constructor.
     * @param string $cwd
     */
    public function __construct($cwd = null)
    {
        $this->cwd = $cwd;
    }

    /**
     * @return array
     */
    public function getList()
    {
        $command = escapeshellarg(self::$bin).' snapshot list';
        $process = new Process($command, $this->cwd, $this->env, null, null);
        $list = [];

        if (!$this->run($process)) {
            $output = $process->getOutput();

            if (!preg_match('/No snapshots/i', $output)) {
                foreach (preg_split('/[\n\r]+/', trim($output)) as $line) {
                    $list[] = trim($line);
                }
            }
        }

        return $list;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function doSave($name)
    {
        $command = escapeshellarg(self::$bin).' snapshot save ';
        $command .= escapeshellarg($name);
        $process = new Process($command, $this->cwd, $this->env, null, null);

        return !$this->run($process);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function doRestore($name)
    {
        $command = escapeshellarg(self::$bin).' snapshot restore ';
        $command .= escapeshellarg($name);
        $process = new Process($command, $this->cwd, $this->env, null, null);

        return !$this->run($process);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function doDelete($name)
    {
        $command = escapeshellarg(self::$bin).' snapshot delete ';
        $command .= escapeshellarg($name);
        $process = new Process($command, $this->cwd, $this->env, null, null);

        return !$this->run($process);
    }

    /**
     * @return bool
     */
    public function doPop()
    {
        $command = escapeshellarg(self::$bin).' snapshot pop';
        $process = new Process($command, $this->cwd, $this->env, null, null);

        return !$this->run($process);
    }

    /**
     * @return bool
     */
    public function doPush()
    {
        $command = escapeshellarg(self::$bin).' snapshot push';
        $process = new Process($command, $this->cwd, $this->env, null, null);

        return !$this->run($process);
    }
}
