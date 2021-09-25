<?php

namespace Fastwf\Tests\Engine;

use Fastwf\Core\Configuration;
use Fastwf\Core\Engine\Engine;

class TestingEngine extends Engine
{

    public function __construct($configurationPath = null) {
        parent::__construct($configurationPath);

        $this->config = new Configuration($configurationPath);
    }
    
    public function getSettings() {
        return [];
    }

}
