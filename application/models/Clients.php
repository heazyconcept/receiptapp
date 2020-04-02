<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Clients extends CI_Model
{

    private $TableName = "clients";

    public function __construct()
    {
        parent::__construct();
        $this->load->model('connectDb');

    }

    public function Insert(array $clientData): int
    {
        try {
            $this->load->library("utilities");

            $clientData["DateCreated"] = $this->utilities->DbTimeFormat();
            // $clientData["IdHash"] = $this->utilities->GenerateGUID();
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $clientData,
            );
            $DbResponse = $this->connectDb->insert_data((object) $dbOptions);
            if (!empty($DbResponse)) {
                return $DbResponse;
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());
            return -1;
        }
        return 0;

    }
     public function Update(stdClass $clientData, int $clientId): int
    {
        try {
            $clientDB = $this->Get($clientId);
            $clientUpdate = array(
                "EmailAddress" => (isset($clientData->EmailAddress) && $clientData->EmailAddress != $clientDB->EmailAddress)? $clientData->EmailAddress : $clientDB->EmailAddress,
                "PhoneNumber" => (isset($clientData->PhoneNumber) && $clientData->PhoneNumber != $clientDB->PhoneNumber)? $clientData->PhoneNumber : $clientDB->PhoneNumber,
                "FullName" => (isset($clientData->FullName) && $clientData->FullName != $clientDB->FullName)? $clientData->FullName : $clientDB->FullName,
                "ResidentialAddress" => (isset($clientData->ResidentialAddress) && $clientData->ResidentialAddress != $clientDB->ResidentialAddress)? $clientData->ResidentialAddress : $clientDB->ResidentialAddress,
                "Company" => (isset($clientData->Company) && $clientData->Company != $clientDB->Company)? $clientData->Company : $clientDB->Company,
                "ModifiedBy" => (isset($clientData->ModifiedBy) && $clientData->ModifiedBy != $clientDB->ModifiedBy)? $clientData->ModifiedBy : $clientDB->ModifiedBy,
                "DateModified" => $this->utilities->DbTimeFormat()
            );
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $clientUpdate,
                "targets" => (object) array("Id" => $clientId)
            );
            $DbResponse = $this->connectDb->modify_data((object) $dbOptions);
            if(!empty($DbResponse))
                return 1;
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return -1;
        }
        return 0;
       
        
        
    }

    public function Get(int $clientId): stdClass
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $clientId),
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult[0];
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return null;

    }
    public function GetWithAll(string $FullName, string $EmailAddress, string $PhoneNumber, string $Company): stdClass
    {
        $targets = array(
            "FullName" => $FullName,
            "EmailAddress" => $EmailAddress,
            "PhoneNumber" => $PhoneNumber,
            "Company" => $Company,
        );
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) $targets,
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult[0];
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return null;

    }
    public function CheckDuplicate(string $EmailAddress): bool
    {
        $targets = array(
            "EmailAddress" => $EmailAddress
            
        );
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) $targets,
            );
            $dbResult = $this->connectDb->count_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return true;
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return false;

    }

    public function Countclients(array $targets = array(), string $operator = ''): int
    {
        try {
            if (empty($targets)) {
                $dbOptions = array(
                    "table_name" => $this->TableName,
                );
            } else {
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
                    } elseif (empty($start)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                        );
                    } else {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "offset" => $start,
                        );
                    }
                }
            } else {
                if (empty($limit) && empty($start)) {
                    $dbOptions = array(
                        "table_name" => $this->TableName,
                        "targets" => (object) $targets,
                    );
                } else {

                    if (empty($limit)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "offset" => $start,
                            "targets" => (object) $targets,
                        );
                    } elseif (empty($start)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "targets" => (object) $targets,
                        );
                    } else {
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
    public function Searchclient(int $limit = 0, int $start = 0, string $SearchParam, $Targets = array()): array
    {

        try {
            $_table = $this->TableName;
            $_targets = '';
            if (!empty($Targets)) {
                $_key = key($Targets[0]);
                $_value = $Targets[0];
                $_targets = " AND $_key = '$_value'";
            }
            $query = "SELECT * FROM $_table WHERE
            CONCAT(FullName, EmailAddress) LIKE '%$SearchParam%' " . $_targets . "
             LIMIT $limit OFFSET $start ";
            $dbOptions = array(
                "my_query" => $query,
                "query_action" => "select",
            );
            $dbResult = $this->connectDb->custom_query((object) $dbOptions);
            return $dbResult;

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
        return array();

    }
    public function SearchclientCount(string $SearchParam, $Targets = array()): int
    {

        try {
            $_table = $this->TableName;
            $_targets = '';
            if (!empty($Targets)) {
                $_key = key($Targets[0]);
                $_value = $Targets[0];
                $_targets = " AND $_key = '$_value'";
            }
            $query = "SELECT * FROM $_table WHERE
            CONCAT(FullName, EmailAddress, PhoneNumber,Company) LIKE '%$SearchParam%' " . $_targets;
            $dbOptions = array(
                "my_query" => $query,
                "query_action" => "select",
            );
            $dbResult = $this->connectDb->custom_query((object) $dbOptions);
            return count($dbResult);

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
        return 0;

    }
    

}

/* End of file clients.php */
