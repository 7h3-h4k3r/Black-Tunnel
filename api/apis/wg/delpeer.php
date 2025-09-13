
<?php
${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['interface']) and isset($this->_request['publickey']) ){
        $wireG = new Wireguard($this->_request['interface']);
        $result = $wireG->delPeer($this->_request['publickey']);
        if($result){
            $data = [
                'message' => 'Peer and credantial\'s Deleted Granted '.$this->_request['interface'],
                'result' => true,
            ];
            $data = $this->json($data);
            $this->response($data, 200);
        } else {
            $data = [
                'message' => 'Peer and credential\'s not in '.$this->_request['interface'],
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