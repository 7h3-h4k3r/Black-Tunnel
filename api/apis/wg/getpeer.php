<?php

${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['interface']) and isset($this->_request['publickey'])){
       $interface = $this->_request['interface'];
       $publickey = $this->_request['publickey'];
        try{
            $s = new wireguard($interface);
            $data = [
                "message" => "success",
                "Peer" => $s->getPeer($publickey)
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