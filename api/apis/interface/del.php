
<?php
${basename(__FILE__, '.php')} = function(){
    if($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['interface'])){
        $interface = $this->_request['interface'];
        $result = Interfaces::del_interface($interface);
        if($result){
            $data = [
                'message' => 'wg-wireguard '.$interface.' Deleted The Interface Granted',
                'result' => $result
            ];
            $data = $this->json($data);
            $this->response($data, 200);
        } else {
            $data = [
                'message' => 'wg-wireguard '.$interface.' Deleted The Interface Failed',
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