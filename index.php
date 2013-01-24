<?php
define("APP_PATH",dirname(__FILE__));
define("SP_PATH",dirname(__FILE__).'/SpeedPHP');
$spConfig = array(
                  'db' => array(),
                  'view' => array(
                                  'enabled' => TRUE,
                                  'config' => array(
                                                    'template_dir' => APP_PATH . '/tpl',
                                                    'compile_dir' => APP_PATH . '/tmp',
                                                    'cache_dir' => APP_PATH .'/tmp',
                                                    'left_delimiter' => '<{',
                                                    'right_delimiter' => '}>')
                                  ),
                  'debug' => FALSE
                  );
require(SP_PATH."/SpeedPHP.php");
spRun();

