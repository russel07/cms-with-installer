<?php
require_once __DIR__.'/DotEnv.php';

class BaseClass{
    use DotEnv;
    protected  $path;
    public $isInstalled;

    public function __construct($path){
        $this->path = $path;
        $this->loadEnv();
        $this->isInstalled();
    }

    public function isInstalled(){
        if(getenv('INSTALLATION') === 'DONE')
            $this->isInstalled = true;
        else $this->isInstalled = false;
    }
}
