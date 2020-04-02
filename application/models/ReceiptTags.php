<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ReceiptTags extends CI_Model
{

    private $TableName = "receipttags";

    public function __construct()
    {
        parent::__construct();
        $this->load->model('connectDb');

    }

    public function ListAll(): array
    {

        try {

           $dbOptions = array(
               "table_name" => $this->TableName
           );
          $dbResult =  $this->connectDb->select_data((object) $dbOptions);
          if(!empty($dbResult)) return $dbResult;

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return array();

    }
   

}

/* End of file ReceiptTags.php */
