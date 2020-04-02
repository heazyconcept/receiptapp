<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Departments extends CI_Model
{

    private $TableName = "departments";

    public function __construct()
    {
        parent::__construct();
        $this->load->model('connectDb');

    }

    public function Insert(array $departmentData): int
    {
        try {
            $this->load->library("utilities");

            $departmentData["DateCreated"] = $this->utilities->DbTimeFormat();
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $departmentData,
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
    public function update(stdClass $departmentData, int $departmentId): int
    {
        try {
            $departmentDB = $this->Get($departmentId);
            $departmentUpdate = array(
                "Department" => (isset($departmentData->Department) && $departmentData->Department != $departmentDB->Department) ? $departmentData->Department : $departmentDB->Department,
                "EmailAddress" => (isset($departmentData->EmailAddress) && $departmentData->EmailAddress != $departmentDB->EmailAddress) ? $departmentData->EmailAddress : $departmentDB->EmailAddress,
                "ModifiedBy" => (isset($departmentData->ModifiedBy) && $departmentData->ModifiedBy != $departmentDB->ModifiedBy) ? $departmentData->ModifiedBy : $departmentDB->ModifiedBy,
                "DateModified" => $this->utilities->DbTimeFormat(),
            );
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $departmentUpdate,
                "targets" => (object) array("Id" => $departmentId),
            );
            $DbResponse = $this->connectDb->modify_data((object) $dbOptions);
            if (!empty($DbResponse)) {
                return 1;
            }

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return -1;
        }
        return 0;

    }
    public function Get(int $departmentId): stdClass
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $departmentId),
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
    public function GetDepartmentEmail(int $departmentId): string
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $departmentId),
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult[0]->EmailAddress;
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return "";

    }
    public function Deletedepartment(int $departmentId): bool
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $departmentId),
            );
            $dbResult = $this->connectDb->delete_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return true;
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return false;

    }

   
   
    public function Countdepartments(array $targets = array(), string $operator = ''): int
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
    public function Searchdepartment(int $limit = 0, int $start = 0, string $SearchParam, $Targets = array()): array
    {

        try {
            $_table = $this->TableName;
            $sec_table = 'clients';
            $_targets = '';
            if (!empty($Targets)) {
                $_key = key($Targets[0]);
                $_value = $Targets[0];
                $_targets = " AND $_key = '$_value'";
            }
            $query = "SELECT a.*, b.FullName, b.EmailAddress, b.PhoneNumber, b.ResidentialAddress, b.Company FROM $_table a inner join $sec_table b on a.ClientId = b.Id where
            CONCAT(a.Amount, b.FullName, b.EmailAddress, b.PhoneNumber, b.ResidentialAddress, b.Company) LIKE '%$SearchParam%' " . $_targets . "
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
    public function SearchdepartmentCount(string $SearchParam, $Targets = array()): int
    {

        try {
            $_table = $this->TableName;
            $sec_table = 'clients';
            $_targets = '';
            if (!empty($Targets)) {
                $_key = key($Targets[0]);
                $_value = $Targets[0];
                $_targets = " AND $_key = '$_value'";
            }
            $query = "SELECT a.*, b.FullName, b.EmailAddress, b.PhoneNumber, b.ResidentialAddress, b.Company FROM $_table a inner join $sec_table b on a.ClientId = b.Id where
            CONCAT(a.Amount, b.FullName, b.EmailAddress, b.PhoneNumber, b.ResidentialAddress, b.Company) LIKE '%$SearchParam%' " . $_targets;
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

/* End of file Departments.php */
