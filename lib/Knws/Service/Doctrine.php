
    <?php

    define('BASEPATH', str_replace('\\', '/', $sys_folder) . '/');
    define('APPPATH',  str_replace('\\', '/', $app_folder) . '/');

    require_once(APPPATH . 'libraries/doctrineORM.php');
    new DoctrineORM();

    // Конфигурируем "Doctrine Cli"
    $config = array(
                    'data_fixtures_path' => APPPATH . '/fixtures',
                    'models_path'         => APPPATH . '/models',
                    'migrations_path'     => APPPATH . '/migrations',
                    'sql_path'            => APPPATH . '/sql',
                    'yaml_schema_path'    => APPPATH . '/schema'
                  );

    $cli = new Doctrine_Cli($config);
    $cli->run($_SERVER['argv']);


/**
* Doctrine initialization class
*/
class DoctrineORM
{
 function __construct() {
    // Получаем конфиг базы данных
    require_once(APPPATH . 'config/database.php');

    // Создаем DSN из полученной инфы
    $db['default']['dsn'] = $db['default']['dbdriver'] .
                            '://' . $db['default']['username'] .
                            ':' . $db['default']['password'].
                            '@' . $db['default']['hostname'] .
                            '/' . $db['default']['database'];

    // Подключаем Doctrine.php
    require_once(BASEPATH . 'database/doctrine/Doctrine.php');

    // Устанавливаем autoloader
    spl_autoload_register(array('Doctrine', 'autoload'));

    // Инициализируем соединение
    Doctrine_Manager::connection($db['default']['dsn'], $db['default']['database']);

    // Устанавливаем тип загрузки моделей в "conservative/lazy"
    Doctrine_Manager::getInstance()->setAttribute('model_loading', 'conservative');

    // Загружаем модели в autoloader
    Doctrine::loadModels(APPPATH . 'models');
 }
}