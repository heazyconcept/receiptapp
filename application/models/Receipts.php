<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Receipts extends CI_Model
{

    private $TableName = "receipts";

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
            if (!empty($DbResponse)) {
                return $DbResponse;
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());
            return -1;
        }
        return 0;

    }
     public function update(stdClass $receiptData, int $receiptId): int
    {
        try {
            $receiptDB = $this->Get($receiptId);
            $receiptUpdate = array(
                "Amount" => (isset($receiptData->Amount) && $receiptData->Amount != $receiptDB->Amount) ? $receiptData->Amount : $receiptDB->Amount,
                "Description" => (isset($receiptData->Description) && $receiptData->Description != $receiptDB->Description) ? $receiptData->Description : $receiptDB->Description,
                "ClientId" => (isset($receiptData->ClientId) && $receiptData->ClientId != $receiptDB->ClientId) ? $receiptData->ClientId : $receiptDB->ClientId,
                "PaymentDetails" => (isset($receiptData->PaymentDetails) && $receiptData->PaymentDetails != $receiptDB->PaymentDetails) ? $receiptData->PaymentDetails : $receiptDB->PaymentDetails,
                "ModeOfPayment" => (isset($receiptData->ModeOfPayment) && $receiptData->ModeOfPayment != $receiptDB->ModeOfPayment) ? $receiptData->ModeOfPayment : $receiptDB->ModeOfPayment,
                "Currency" => (isset($receiptData->Currency) && $receiptData->Currency != $receiptDB->Currency) ? $receiptData->Currency : $receiptDB->Currency,
                "DepartmentId" => (isset($receiptData->DepartmentId) && $receiptData->DepartmentId != $receiptDB->DepartmentId) ? $receiptData->DepartmentId : $receiptDB->DepartmentId,
                "ReceiptTag" => (isset($receiptData->ReceiptTag) && $receiptData->ReceiptTag != $receiptDB->ReceiptTag) ? $receiptData->ReceiptTag : $receiptDB->ReceiptTag,
                "ModifiedBy" => $this->utilities->GetSessionId(),
                "DateModified" => $this->utilities->DbTimeFormat(),
            );
            $dbOptions = array(
                "table_name" => $this->TableName,
                "process_data" => (object) $receiptUpdate,
                "targets" => (object) array("Id" => $receiptId),
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

    public function Get(int $receiptId): stdClass
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $receiptId),
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
    public function GetByReceiptId(string $receiptId): stdClass
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("ReceiptId" => $receiptId),
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
    public function GetLastInsertedIdByCategory(int $categoryId): string
    {
        try {
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("CategoryId" => $categoryId),
                "filter_key" => "DateCreated",
                "filter_value" => "Desc",
                "limit" => 1,

            );
            $dbResult = $this->connectDb->select_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult[0]->ReceiptNumber;
            }

        } catch (\Throwable $th) {

            log_message('error', $th->getMessage());

        }
        return 0;

    }

    public function ApproveReceipt(int $receiptId): int
    {
        try {
            $receiptData = array(
                "TransactionState" => "Approved",
                "ModifiedBy" => $this->utilities->GetSessionId(),
                "DateModified" => $this->utilities->DbTimeFormat(),
            );
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $receiptId),
                "process_data" => $receiptData,
            );
            $dbResult = $this->connectDb->modify_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult;
            }

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return -1;
        }
        return 0;

    }
    public function DeclineReceipt(int $receiptId, string $rejectReason): int
    {
        try {
            $receiptData = array(
                "TransactionState" => "Declined",
                "RejectReason" => $rejectReason,
                "ModifiedBy" => $this->utilities->GetSessionId(),
                "DateModified" => $this->utilities->DbTimeFormat(),
            );
            $dbOptions = array(
                "table_name" => $this->TableName,
                "targets" => (object) array("Id" => $receiptId),
                "process_data" => $receiptData,
            );
            $dbResult = $this->connectDb->modify_data((object) $dbOptions);
            if (!empty($dbResult)) {
                return $dbResult;
            }

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
            return -1;
        }
        return 0;

    }

    public function Countreceipts(array $targets = array(), string $operator = ''): int
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
                        "filter_key" => "DateCreated",
                        "filter_value" => "desc",
                    );
                } else {

                    if (empty($limit)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "offset" => $start,
                            "filter_key" => "DateCreated",
                            "filter_value" => "desc",
                        );
                    } elseif (empty($start)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "filter_key" => "DateCreated",
                            "filter_value" => "desc",
                        );
                    } else {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "offset" => $start,
                            "filter_key" => "DateCreated",
                            "filter_value" => "desc",
                        );
                    }
                }
            } else {
                if (empty($limit) && empty($start)) {
                    $dbOptions = array(
                        "table_name" => $this->TableName,
                        "targets" => (object) $targets,
                        "filter_key" => "DateCreated",
                        "filter_value" => "desc",
                    );
                } else {

                    if (empty($limit)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "offset" => $start,
                            "targets" => (object) $targets,
                            "filter_key" => "DateCreated",
                            "filter_value" => "desc",
                        );
                    } elseif (empty($start)) {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "targets" => (object) $targets,
                            "filter_key" => "DateCreated",
                            "filter_value" => "desc",
                        );
                    } else {
                        $dbOptions = array(
                            "table_name" => $this->TableName,
                            "limit" => $limit,
                            "offset" => $start,
                            "targets" => (object) $targets,
                            "filter_key" => "DateCreated",
                            "filter_value" => "desc",
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
    public function SearchTag(string $SearchParam):array
    {
        try {
            $_table = $this->TableName;
            $query = "SELECT DISTINCT ReceiptTag AS TagName FROM $_table WHERE ReceiptTag LIKE '%$SearchParam%' LIMIT 10";
            $dbOptions = array(
                "my_query" => $query,
                "query_action" =>"select"
            );
            $dbResult = $this->connectDb->custom_query((object) $dbOptions);
            return $dbResult;
        }catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
        return array();
    }
    public function Searchreceipt(int $limit = 0, int $start = 0, string $SearchParam, $Targets = array()): array
    {

        try {
            $_table = $this->TableName;
            $_targets = '';
            if (!empty($Targets)) {
                $_key = key($Targets[0]);
                $_value = $Targets[0];
                $_targets = " AND $_key = '$_value'";
            }
            $query = "SELECT a.*, b.FullName,b.EmailAddress, b.PhoneNumber, c.Category FROM $_table a
            inner join clients b on a.ClientId = b.Id
            inner join receiptcategories c on a.CategoryId = c.Id  WHERE
            CONCAT(b.FullName, b.EmailAddress, b.PhoneNumber,a.ReceiptId,a.Amount,a.TransactionState) LIKE '%$SearchParam%' " . $_targets . "
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
    public function SearchreceiptCount(string $SearchParam, $Targets = array()): int
    {

        try {
            $_table = $this->TableName;
            $_targets = '';
            if (!empty($Targets)) {
                $_key = key($Targets[0]);
                $_value = $Targets[0];
                $_targets = " AND $_key = '$_value'";
            }
            $query = "SELECT a.*, b.FullName,b.EmailAddress, b.PhoneNumber, c.Category FROM $_table a
            inner join clients b on a.ClientId = b.Id
            inner join receiptcategories c on a.CategoryId = c.Id  WHERE
            CONCAT(b.FullName, b.EmailAddress, b.PhoneNumber,a.ReceiptId,a.Amount,a.TransactionState) LIKE '%$SearchParam%' " . $_targets;
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
