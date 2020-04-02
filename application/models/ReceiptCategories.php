<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class ReceiptCategories extends CI_Model {
    
    private $TableName = "receiptcategories";
   
   public function __construct()
   {
       parent::__construct();
       $this->load->model('connectDb');
       
   }
   
    public function Insert(array $receiptData): int
    {
        try {
            $this->load->library("utilities");
            $receiptData["DateCreated"] = $this->utilities->DbTimeFormat();
            // $receiptData["IdHash"] = $this->utilities->GenerateGUID();
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $receiptData,
            );
            $DbResponse = $this->connectDb->insert_data((object) $dbOptions);
            if(!empty($DbResponse))
                return $DbResponse;
        } catch (\Throwable $th) {
            
            log_message('error', $th->getMessage());
            return -1;
        }
       return 0;
        
    }
    
    public function Get(int $catId): stdClass
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $catId)
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if(!empty($dbResult))
                return $dbResult[0];
    
            
        } catch (\Throwable $th) {
            
            log_message('error', $th->getMessage());
        
        }
        return null;
        
    }
     public function GetAbbreviation(int $catId): string
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $catId)
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if(!empty($dbResult))
                return $dbResult[0]->Abbreviation;
            
        } catch (\Throwable $th) {
            
            log_message('error', $th->getMessage());
        
        }
        return "";
        
    }
    public function GetByAbbreviation(string $abbreviation): stdClass
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Abbreviation" => $abbreviation)
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if(!empty($dbResult))
                return $dbResult[0];
    
            
        } catch (\Throwable $th) {
            
            log_message('error', $th->getMessage());
        
        }
        return null;
        
    }
    

    public function Countcategories(array $targets = array(), string $operator = ''): int
    {
        try {
            if (empty($targets)) {
                $dbOptions = array(
                    "table_name" => $this->TableName,
                );
            }else {
                $dbOptions = array(
                    "table_name" => $this->TableName,
                    "targets" => $targets,
                    "operator" => $operator,
                );
            }
            
            $DbResponse = $this->connectDb->count_data((object) $dbOptions);
            return $DbResponse;
         } catch (\Throwable $th) {
            log_message('error', $th->getMessage()); 
        }
        return 0;
    
    }
    public function ListAll(int $limit = 0, int $start = 0, array $targets = array()): array
    {
        try {
            
            if (empty($targets)) {
                if (empty($limit) && empty($start)) {
                    $dbOptions = array(
                        "table_name" => $this->TableName,
                    );
                } else {

                    if (empty($limit)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "offset" => $start,
                        );
                    }elseif (empty($start)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                        );
                    }else {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "offset" => $start,
                        );
                    }
                }
            }else {
                if (empty($limit) && empty($start)) {
                    $dbOptions = array(
                        "table_name" => $this->TableName,
                        "targets" => (object) $targets
                    );
                } else {

                    if (empty($limit)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "offset" => $start,
                            "targets" => (object) $targets,
                        );
                    }elseif (empty($start)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "targets" => (object) $targets,
                        );
                    }else {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "offset" => $start,
                            "targets" => (object) $targets,
                        );
                    }
                }
            }
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
                return $dbResult;
    
            
        } catch (\Throwable $th) {
            
            log_message('error', $th->getMessage());
        
        }
        return array();
        
    }
    public function Searchcategories(int $limit = 0, int $start = 0, string $SearchParam , $Targets = array() ): array
    {
        
        try {
            $_table = $this->TableName;
            $_targets = '';
            if(!empty($Targets)){
                $_key = key($Targets[0]);
                $_value = $Targets[0];
                $_targets = " AND $_key = '$_value'";
            }
            $query = "SELECT * FROM $_table WHERE AccountType != 'Admin' and 
            CONCAT(FullName, EmailAddress, PhoneNumber,Country) LIKE '%$SearchParam%' ". $_targets ." 
             LIMIT $limit OFFSET $start ";
            $dbOptions = array(
                "my_query" => $query,
                "query_action" => "select"
            );
            $dbResult = $this->connectDb->custom_query((object) $dbOptions);
            return $dbResult;

        } catch (\Throwable $th) {
             log_message('error', $th->getMessage());
        }
        return array();

    }
    public function SearchreceiptCount( string $SearchParam, $Targets = array()): int
    {
        
        try {
            $_table = $this->TableName;
            $_targets = '';
            if(!empty($Targets)){
                $_key = key($Targets[0]);
                $_value = $Targets[0];
                $_targets = " AND $_key = '$_value'";
            }
            $query = "SELECT * FROM $_table WHERE AccountType != 'Admin' and 
            CONCAT(FullName, EmailAddress, PhoneNumber,Country) LIKE '%$SearchParam%' ". $_targets;
            $dbOptions = array(
                "my_query" => $query,
                "query_action" => "select"
            );
            $dbResult = $this->connectDb->custom_query((object) $dbOptions);
            return count($dbResult);

        } catch (\Throwable $th) {
             log_message('error', $th->getMessage());
        }
        return 0;

    }
   



}

/* End of file ReceiptCategories.php */



?>