<?php

${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['interface'])){
       $interface = $this->_request['interface'];
        try{
            $s = new wireguard($interface);
            $data = [
                "message" => "success",
                "Peers" => $s->getPeers()
            ];
            $this->response($this->json($data), 200);
        } catch(Exception $e) {
            $data = [
                "error" => $e->getMessage()
            ];
            $this->response($this->json($data), 409);
        }
         
    } else {
        $data = [
            "error" => "Bad request"
        ];
        $data = $this->json($data);
        $this->response($data, 400);
    }
};