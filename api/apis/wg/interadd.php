
<?php
${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['interface']) and isset($this->_request['cidr']) and isset($this->_request['port'])){
        $inter = new Interfaces($this->_request['interface'],$this->_request['cidr'],$this->_request['port']);
        unset($inter);
        if(Interfaces::$result){
            $data = [
                'message' => 'success',
            ];
            $data = $this->json($data);
            $this->response($data, 200);
        } else {
            $data = [
                'message' => 'error',
                'result'=>Interfaces::$result
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