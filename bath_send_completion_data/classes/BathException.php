<?php

namespace local_bath_send_completion_data;
class BathException extends \Exception
{
    public function taskException(){
        $errorMsg = "\n Line ".$this->getLine().":Error Message:".$this->getMessage();
        return $errorMsg;
    }
}