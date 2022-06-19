<?php

namespace App;

abstract class Robot
{
    protected $logFile;

    protected function isRobotLock(string $class): bool
    {
        static $lock;
        $is_lock    = true;
        $lock_file  = md5($class).".txt";
        $lock       = fopen( __DIR__."/../locks/".$lock_file, 'w+');
        if (flock($lock, LOCK_EX | LOCK_NB)) {
            $is_lock = false;
        }
        return $is_lock;
    }

    abstract public function execute(): void;
}