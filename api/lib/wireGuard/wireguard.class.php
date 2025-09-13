<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database/Database.class.php');

class Wireguard
{
    private $db = NULL;
    private $interface = NULL;
    private $output;
    private $return;
    public function __construct($interface){
        if($this->db == NULL){
            $this->db = Database::getMongoConn();
        }
        $this->interface = $interface;

    }

    /* 
        getUserData($email, $publicket) , $emial must be unique and if public key has been Null Create a privateKey and PublicKey on serverside // update 
        on 15/oct/2025 v1.2

        currently available only $email = unique and not null $publicKey = not !equal to Null and UNIQUE
        on (version v1.0)
    */

    public function setUserData($email,$publicKey)
    {
        // if($publicKey == NULL){
        //     //future Update
        // }
        $allocation_ip = $this->getNextIP();
        try{

            $result = $this->db->networks->{$this->interface}->updateOne(
                ['ip' => $allocation_ip],
                ['$set' => ['owner' => $email,'public_key' => $publicKey,'active_at' => date('l jS \of F Y h:i:s A')]]
            );
                
            if($result->getMatchedCount() == $result->getModifiedCount()){
                $this->addPeer($allocation_ip,$publicKey);
                return $allocation_ip;
            }
        }
        catch (Exception $e){
            $error = $e->getMessage();
            if (strpos($error,'E11000') !== FALSE){
                throw new Exception('Credantial Failed [!] invalidate Email or PublicKey');
            }
            else{
                throw new Exception($error);
            }
        }
    }


    /* getNextIP () 
    1. find a publicKey = False on decending order 
    2. get Ip first and return 
    
    get data from data base filter which publicKey  = false and five that IP 
    we use the ip to add a peer
    */
    private function getNextIP(){
        try{
            $result = $this->db->networks->{$this->interface}->findOne(
                ['public key' => null],
                [
                    'projection' => ['ip'=>1],
                    'sort' => ['_id'=>1]
                ]
            );
            return $result['ip'];
        }
        catch (Exception $e)
        {
            throw new Exception('Interface not have the that CIDR');
        }
    }

    /* add peer on wireGuard help of shell_exe() or exec() funciton's
    return connection details if success or retunr peer add failed*/
    private function addPeer($ip,$publicKey){
        $cmd = "sudo wg set ".$this->interface." peer "."\"$publicKey\""." allowed-ips ".$ip;
        try{
            exec($cmd,$this->output,$this->return);

            if($this->return == 0){
               return true;
            }
            else{
               throw new Exception('authentication failed try different wireguard PublicKey');
            }
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }


    }


    /* Deleted peer on wireGuard help of shell_exe() or exec() funciton's
     and return 0 if success or return -1*/
    public function delPeer($publicKey){
        try{
            $result = $this->db->networks->{$this->interface}->updateOne(
                ['public_key' => $publicKey],
                ['$set' => ['owner' => null,'public_key' => null,'create_at' => date('l jS \of F Y h:i:s A').'{reCreate}']]
                );
            $cmd =  "sudo wg set "."$this->interface"." peer "."\"$publicKey\""." remove";
            if($result->getModifiedCount()!=0){
                exec($cmd,$this->output,$this->return);
                return true;
            }
           
            return false;
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /* checking  single Peer is active or not or any data trastation Error  */
    public function getpeer($publicKey){

    }
    
    //
    /* give all peers 30 - 30*/
    public function getPeers(){
        $output = shell_exec("sudo wg show ".$this->interface);    
        $output = explode(PHP_EOL,$output);
        $result = array();
        $index = 0;
        foreach($output as $value){
            if(!empty(trim($value))){
                $value = explode(":",trim($value));
                $result[$index][$value[0]] = $value[1];
            }else{
                $index++;
            }
            
        }
        return $result;
    
    }


    /* its deleted all un-available Data's on
    WireGuard interface , and return all deleted data Once
    
    repair the Interface ;)*/
    public function repairPeers($interface){

    }
}