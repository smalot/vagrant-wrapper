<?php

namespace Smalot\Vagrant\Wrapper;

use Symfony\Component\Process\Process;

/**
 * Class CommandTrait
 * @package Smalot\Vagrant\Wrapper
 */
trait CommandTrait
{
    /**
     * @var string
     */
    protected static $bin = 'vagrant';

    /**
     * @var string
     */
    protected $lastOutput;

    /**
     * @param string $bin
     */
    public static function setBinPath($bin)
    {
        self::$bin = $bin;
    }

    /**
     * @return string
     */
    public function __getLastOutput()
    {
        return $this->lastOutput;
    }

    /**
     * @param Process $process
     * @return int
     */
    protected function run(Process $process)
    {
        return $process->run(
          function ($type, $output) {
              $this->lastOutput .= $output;
          }
        );
    }
}
