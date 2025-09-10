<?php
require $_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php';
class Database {
    static $db;
    static $mongodb;
    private $config;

    // private function getConfig(){
        
    // }
    
    public static function getConnection(){
        $config_json = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../env.json');
        $config = json_decode($config_json, true);
        if (Database::$db != NULL) {
            return Database::$db;
        } else {
            Database::$db = mysqli_connect($config['server'],$config['username'],$config['password'], $config['database']);
            if (!Database::$db) {
                die("Connection failed: ".mysqli_connect_error());
            } else {
                return Database::$db;
            }
        }
    }
       
     public static function getMongoConn(){
        $config_json = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../env.json');
        $config = json_decode($config_json, true);
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



    public static function setIndex($collectionName) {
    try {
        $client = new MongoDB\Client("mongodb://127.0.0.1:27017/?directConnection=true&serverSelectionTimeoutMS=2000&appName=mongosh+2.5.7");

        $command = new MongoDB\Driver\Command([
            'createIndexes' => $collectionName,
            'indexes' => [
                [
                    'key' => ['owner' => 1],
                    'name' => 'unique_owner',
                    'unique' => true,
                    'partialFilterExpression' => [
                        'owner' => ['$exists' => true, '$ne' => null]
                    ]
                ],
                [
                    'key' => ['public_key' => 1],
                    'name' => 'unique_public_key',
                    'unique' => true,
                    'partialFilterExpression' => [
                        'public_key' => ['$exists' => true, '$ne' => null]
                    ]
                ]
            ]
        ]);

        $client->networks->executeCommand($collectionName,$command);

    } catch (Exception $e) {
        throw new Exception('Report to admin: ' . $e->getMessage());
    }
}


    
}
