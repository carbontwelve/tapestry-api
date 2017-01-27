<?php

namespace App\Factories;

use Symfony\Component\Console\Input\ArrayInput;
use Tapestry\Console\DefaultInputDefinition;
use Tapestry\Tapestry;

class TapestryCoreFactory
{
    public function build($attr = []) {
        /*[
            '--env' => 'local',
            '--site-dir' => APP_BASE . '/test-site',
            '--dist-dir' => APP_BASE . '/storage/dist-local'
        ]*/
        return new Tapestry(new ArrayInput($attr, new DefaultInputDefinition()));
    }
}
