
<?php
${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['interface']) and isset($this->_request['publickey']) and isset($this->_request['email']) ){
        $wireG = new Wireguard($this->_request['interface']);
        $result = $wireG->setUserData($this->_request['email'],$this->_request['publickey']);
        if(!$result){
            $data = [
                'message' => 'peer added to the Interface'.$this->_request['interface'],
                'result' => true,
                'allocation IP' => $result 
            ];
            $data = $this->json($data);
            $this->response($data, 200);
        } else {
            $data = [
                'message' => 'peer  Failed to insert Interface'.$this->_request['interface'],
                'result'=>$result
            ];
            $data = $this->json($data);
            $this->response($data, 400);
        }
       
    } else {
        $data = [
            "error" => "Bad request"
        ];
        $data = $this->json($data);
        $this->response($data, 400);
    }
};