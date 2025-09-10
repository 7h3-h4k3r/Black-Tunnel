<?php
require $_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php';
class Database {
    static $db;
    static $mongodb;
    private $config;

    private function getConfig(){
        $config_json = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../env.json');
        $this->config = json_decode($config_json, true);
    }
    
    public static function getConnection(){
        $this->getConfig();
        if (Database::$db != NULL) {
            return Database::$db;
        } else {
            Database::$db = mysqli_connect($this->config['server'],$this->config['username'],$this->config['password'], $this->config['database']);
            if (!Database::$db) {
                die("Connection failed: ".mysqli_connect_error());
            } else {
                return Database::$db;
            }
        }
    }
       
     public static function getMongoConn(){
        if (Database::$mongodb != NULL) {
            return Database::$mongodb;
        } else {
            try{
               

                Database::$mongodb = new MongoDB\Client("mongodb://127.0.0.1:27017/?directConnection=true&serverSelectionTimeoutMS=2000&appName=mongosh+2.5.7");
                Database::$mongodb->vpn->wireguard->createIndex(['ip'  => 1,],['unique' => true]);
                Database::$mongodb->vpn->wireguard->createIndex(['Interface'  => 1,],['unique' => true]);
                Database::$mongodb->vpn->wireguard->createIndex(['port'  => 1,],['unique' => true]);    
            return Database::$mongodb;
            }
            catch(Exception $e){
                throw new Exception($e->getMessage());
            }

        }
    }
    public static function setIndex($db){
        $manger = new MongoDB\Client("mongodb://127.0.0.1:27017/?directConnection=true&serverSelectionTimeoutMS=2000&appName=mongosh+2.5.6");
        $command = new MongoDB\Driver\Command([
            'createIndexes' => $db,
            'indexes' => [
                [
                    'key' => ['owner' => 1],
                    'owner' => 'unique_owner',
                    'unique' => true,
                    'sparse' => true
                ],
                [
                    'key' => ['public key' => 1],
                    'public key' => 'unique_public key',
                    'unique' => true,
                    'sparse' => true
                ]
                ]
            ]);
            
            $manger->networks->executeCommand($db, $command);
    }
    
}
