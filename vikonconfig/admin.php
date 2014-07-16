<?php

if (!defined('DOKU_INC'))
{
    die();
}

define('PLUGIN_VIKONCONFIG_INC', true);

class admin_plugin_vikonconfig extends DokuWiki_Admin_Plugin
{
    /** @var  admin_plugin_config */
    private $admin_plugin_config;

    public function __construct()
    {
        $this->admin_plugin_config = plugin_load('admin', 'config');
    }

    public function html()
    {
        include __DIR__ . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'admin.php';
    }

    public function __get($name)
    {
        return $this->admin_plugin_config->$name;
    }

    public function __set($name, $value)
    {
        return $this->admin_plugin_config->$name = $value;
    }

    public function __call($name, $arguments)
    {
        return $this->admin_plugin_config->$name($arguments);
    }

    public function getTOC()
    {
        return $this->admin_plugin_config->getTOC();
    }
}
