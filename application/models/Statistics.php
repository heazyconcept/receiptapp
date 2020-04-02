<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends CI_Model {
    
   
   
   public function __construct()
   {
       parent::__construct();
       $models  = array('connectDb', 'users', 'clients', 'receipts');
       $this->load->model($models);
       
   }
   
    public function GetTotalClients(array $targets = array()): int
    {
        try {
            if (empty($targets)) {
                return $this->clients->Countclients();
            }else {
                return $this->clients->Countclients($targets,'AND');
            }
           
        } catch (\Throwable $th) {
            
            log_message('error', $th->getMessage());
            return -1;
        }
       return 0;
        
    }
     public function GetTotalReceipt(array $targets = array()): int
    {
        try {
            if (empty($targets)) {
                return $this->receipts->Countreceipts();
            }else {
                return $this->receipts->Countreceipts($targets, "AND");
            }
           
        } catch (\Throwable $th) {
            
            log_message('error', $th->getMessage());
            return -1;
        }
       return 0;
        
    }
    public function GetPendingReceipt(array $targets = array()): int
    {
        try {
            $targets["TransactionState"] = "Pending"; 
                return $this->receipts->Countreceipts($targets, "AND");
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return -1;
        }
       return 0;
        
    }
   



}

/* End of file ModelName.php */



?>