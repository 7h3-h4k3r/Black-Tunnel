<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database/Database.class.php');
class Interfaces
{
    private $interface_name;
    private $cidr;
    private $port;
    public  $db = NULL;
    public static $result = false;
    private $filename;
    public  function __construct($interface_name,$cidr,$port)
    {
        $this->interface_name = $interface_name;
        $this->port = (int)$port;
        
        try{
            if($this->db == NULL)
            {
                $this->db = Database::getMongoConn();
            }
            if($this->port > 65535)
            {
                throw new Exception('Port is Invalied [info ]In computer networking, port numbers range from 0 to 65535 ');
            }
        $this->cidrAuth($cidr.'/24');
        $this->inIpPort();
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
        
    }

    
    public function getGatway($ip,$cidr){
        $ipLong = ip2long($ip);
        $mask = -1 << (32 - $cidr);
        $network = $ipLong & $mask;
        $gatewayLong = $network + 1;
        return long2ip($gatewayLong);
    }

    public function cidrAuth($cidr)
    {
        $pattern = '/^((25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)\.){3}(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)\/([0-9]|[1-2][0-9]|3[0-2])$/';
        try{
            if (preg_match($pattern, $cidr, $matches)){
                $this->cidr = $cidr;
            }
            else{
                throw new Exception('cidr notaion Invalid Format');
            }
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }

            
    }
    /*check the Address is already User ot Not
            if address is user set Result = Flase
            if address is available Result not any change's
    */
    public function inIpPort()
    {

        $subnet = explode('/',$this->cidr);
        $gatway = $this->getGatway($subnet[0],$subnet[1]);
        $this->cidr = $gatway.'/'.$subnet[1];
        try{
            $result = $this->db->vpn->wireguard->insertOne(
                [
                'cidr'=>$subnet[1],
                'ip' =>$gatway,
                'Interface'=>$this->interface_name,
                'port'=>$this->port
                ]
            );
            
        }
        catch (MongoDB\Driver\Exception\Exception $e)
        {
            $error = $e->getMessage();
            if (strpos($error,'E11000') !== FALSE){
                if (strpos($error,'ip_1') !== FALSE){
                    throw new Exception('Ip Address already Used');
                }
                elseif(strpos($error,'Interface_1')!== FALSE)
                {
                    throw new Exception('Interface already used');
                }
                elseif (strpos($error,'port_1') !== FALSE)
                {
                    throw new Exception('Port Already Used');
                }
                else{
                    throw new Exception($error);
                }
            }
        }
    
    } 


    
    private function ipList()
    {   
        /* this function for admin Users*/ 
    }

    /* crate  a backfile of list of ips , once create a ips i prementally saved on ipsList Folder 
        beacaue creat a ips list its take hudege amount of time ex:[172.168.0.1/16] 
        if the already use the ips , its retune ips file name or ceate a name and 
        full the ips with the help of calculate-ip.c <- its small amout of Tool it help 
        to make list of ips , avoid the the pain of Creat all availabe ips /16 
    */
    public function cidrFile()
    {
    
        $filename = str_replace('.','_',$this->cidr);
        $filename = str_replace('/','-',$filename);
        return 'cidr/'.$filename.'.txt';
    }


    /* getIPS -> run the exe c file to get a all ips and  and store a fileName if 
    scuccessfully stored on ip file its return a True or $result = False*/
    public function getIPS()
    {
        $output = NULL;
        $return = NULL;
        $filename = $this->cidrFile();
        if(file_exists($filename))
        {
            return $filename;
        }
        exec('cd '.$_SERVER['DOCUMENT_ROOT'].'/drivers && ./exe '.$this->cidr.' > ../'.$filename,$output,$return);
        if($return == 0){
            return  $filename;
        }
    

        return -1;
    }
    /*  lets set a data on Mongo db Database 
        but Before run inIpPort()
        lets get a all available ips with the help of ipList() 
        that give a array -> array data move into Mongo ip insert Many(array)
    */ 
    private  function setConfiguration()
    {
        $result  = $output = 0;
        exec('cd '.$_SERVER['DOCUMENT_ROOT'].'/wgctl && ./main.py '.$this->interface_name.' '.$this->cidr.' '.$this->port,$output,$return);
        if ($result==0){
            return true;
        }
        else{
            return false;
        }
    }
    public static function up_interface($interface)
    {
        $result  = $output = 0;
        
        try{
            exec('cd '.$_SERVER['DOCUMENT_ROOT'].'/wgctl && ./up.py '.$interface,$output,$return);
            if ($return==0){
                return true;
            }
        }
        catch (Exception  $e){
            throw new Exception($e->getMessage());
        }
    }
    public static function del_interface($interface)
    {
        $result  = $output = 0;
        $db = Database::getMongoConn();
        try{
            exec('cd '.$_SERVER['DOCUMENT_ROOT'].'/wgctl && ./removeInterface.py '.$interface,$output,$return);
            if ($return==0){
                $result =  true;
            }
            $result = $db->vpn->wireguard->deleteOne(['Interface' => $interface]);
            $result = $result->getDeletedCount();
            if ($result > 0) {
                $result = $db->networks->{$interface}->drop();
            } 
            return $result;
        }
        catch (Exception  $e){
            throw new Exception($e->getMessage());
        }
        
    }

    public static function down_interface($interface)
    {
        $result  = $output = 0;
    
        try{
            exec('cd '.$_SERVER['DOCUMENT_ROOT'].'/wgctl && ./down.py '.$interface,$output,$return);
            if ($return==0){
                $result =  true;
            }
        }
        catch (Exception  $e){
            throw new Exception($e->getMessage());
        }

        
    }



    public function  __destruct(){
        $ips_arr = array();
        $count = -1;
        $filehandle = NULL;
        $filename = $this->getIPS();
        if($filename ==-1 && $this->result !=True){
            throw new Exception('configuration Failed');
        }
        try{
            $filehandle = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$filename,'r');

            if($filehandle)
            {
                while(!feof($filehandle))
                {
                    $line = fgets($filehandle);
                    
                    if(!empty($line)){
                        $ip = trim($line).'/32';
                        $count = $count + 1;
                        array_push($ips_arr,['_id'=> $count,'ip'=>$ip,'owner'=>null,'public_key'=>null,'folder'=>$filename,'create_at' => time(),'active_at' =>'']);
                    }
                }
            }
            else{
                throw new Exception('file problem');
            }
            $result = $this->db->networks->{$this->interface_name}->insertMany($ips_arr);
            Database::setIndex($this->interface_name);
            if ($this->setConfiguration()){
                self::$result = true;
            }
            else{
                throw new Exception('server side configuration failed');
            }
            fclose($filehandle);
        }
        
        catch (Exception $e)
        {
            self::$result = FALSE;
            throw new Exception($e->getMessage());
        }
    }
}