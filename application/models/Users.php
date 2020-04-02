<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Model
{

    private $TableName = "users";

    public function __construct()
    {
        parent::__construct();
        $this->load->model('connectDb');

    }

    public function Insert(array $UserData): int
    {
        try {
            $this->load->library("utilities");

            $token = md5($UserData['Password']);
            $UserData['Password'] = hash("sha512", $token . $UserData['Password']);
            $UserData["Token"] = $token;
            $UserData["DateCreated"] = $this->utilities->DbTimeFormat();
            // $UserData["IdHash"] = $this->utilities->GenerateGUID();
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $UserData,
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
    public function update(stdClass $UserData, int $UserId): int
    {
        try {
            $UserDB = $this->Get($UserId);
            $UserUpdate = array(
                "EmailAddress" => (isset($UserData->EmailAddress) && $UserData->EmailAddress != $UserDB->EmailAddress) ? $UserData->EmailAddress : $UserDB->EmailAddress,
                "PhoneNumber" => (isset($UserData->PhoneNumber) && $UserData->PhoneNumber != $UserDB->PhoneNumber) ? $UserData->PhoneNumber : $UserDB->PhoneNumber,
                "FullName" => (isset($UserData->FullName) && $UserData->FullName != $UserDB->FullName) ? $UserData->FullName : $UserDB->FullName,
                "ModifiedBy" => (isset($UserData->ModifiedBy) && $UserData->ModifiedBy != $UserDB->ModifiedBy) ? $UserData->ModifiedBy : $UserDB->ModifiedBy,
                "DateModified" => $this->utilities->DbTimeFormat(),
            );
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $UserUpdate,
                "targets" => (object) array("Id" => $UserId),
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
    public function Get(int $UserId): stdClass
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $UserId),
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
    public function GetName(int $UserId): string
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $UserId),
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult[0]->FullName;
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return "";

    }
    public function DeleteUser(int $UserId): bool
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $UserId),
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

    public function GetByEmail(string $EmailAddress): stdClass
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("EmailAddress" => $EmailAddress),
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult[0];
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return (object) array();

    }
    public function ListByEmail(string $EmailAddress): array
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("EmailAddress" => $EmailAddress),
            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult;
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return  array();

    }
    public function ChangePassword(string $Password, int $UserId): int
    {
        try {
            $token = md5($Password);
            $Password = hash("sha512", $token . $Password);
            $UserUpdate = array(
                "Password" => $Password,
                "Token" => $token,
                "IsPasswordChanged" => 1,
                "ModifiedBy" => $UserId,
                "DateModified" => $this->utilities->DbTimeFormat(),
            );
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $UserUpdate,
                "targets" => (object) array("Id" => $UserId),
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
    public function ConfirmPassword(string $Password, int $UserId): bool
    {
        try {
            $userdata = $this->Get($UserId);
            $token = md5($Password);
            $enteredPassword = hash("sha512", $token . $Password);
            if ($userdata->Password == $enteredPassword) {
                return true;
            }

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());

        }
        return false;

    }

    public function CheckExist($EmailAddress, $PhoneNumber): bool
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => array("EmailAddress" => $EmailAddress, "PhoneNumber" => $PhoneNumber),
                "operator" => "OR",
            );
            $DbResponse = $this->connectDb->count_data((object) $dbOptions);
            if (empty($DbResponse)) {
                return false;
            }

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return false;

        }
        return true;

    }
    public function CountUsers(array $targets = array(), string $operator = ''): int
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
    public function SearchUser(int $limit = 0, int $start = 0, string $SearchParam, $Targets = array()): array
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
    public function SearchUserCount(string $SearchParam, $Targets = array()): int
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

/* End of file ModelName.php */
