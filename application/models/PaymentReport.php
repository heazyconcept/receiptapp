<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PaymentReport extends CI_Model
{

    private $TableName = "paymentreport";

    public function __construct()
    {
        parent::__construct();
        $this->load->model('connectDb');

    }

    public function ListAll(stdClass $parameters): array
    {

        try {
            $query = "";
            $_table = $this->TableName;
            $departmentQuery = ($parameters->department == 99) ? "" : " AND DepartmentId = {$parameters->department}";
            $categoryQuery = ($parameters->category == 99) ? "" : " AND CategoryId = {$parameters->category}";
            if (isset($parameters->limit) || isset($parameters->start)) {
                $query = "SELECT * FROM $_table where DateCreated >= DATE('{$parameters->from}') AND DateCreated <= DATE('{$parameters->to}')
            {$departmentQuery} {$categoryQuery} ORDER BY DateCreated asc  LIMIT {$parameters->limit} OFFSET {$parameters->start} ";
            } else {
                $query = "SELECT * FROM $_table where DateCreated >= DATE('{$parameters->from}') AND DateCreated <= DATE('{$parameters->to}')
            {$departmentQuery} {$categoryQuery} ORDER BY DateCreated asc";
            }
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
    public function SearchPayments(stdClass $parameters): array
    {

        try {
            $query = "";
            $_table = $this->TableName;
            $departmentQuery = ($parameters->department == 99) ? "" : " AND DepartmentId = {$parameters->department}";
            $categoryQuery = ($parameters->category == 99) ? "" : " AND CategoryId = {$parameters->category}";
            if (isset($parameters->limit) || isset($parameters->start)) {
                $query = "SELECT * FROM $_table where DateCreated >= DATE('{$parameters->from}') AND DateCreated <= DATE('{$parameters->to}')
                AND CONCAT(Amount, ClientName, ClientPhone, ClientEmail, IssuedBy) LIKE '%{$parameters->search}%'
            {$departmentQuery} {$categoryQuery}  ORDER BY DateCreated asc LIMIT {$parameters->limit} OFFSET {$parameters->start} ";
            } else {
                $query = "SELECT * FROM $_table where DateCreated >= DATE('{$parameters->from}') AND DateCreated <= DATE('{$parameters->to}')
                AND CONCAT(Amount, ClientName, ClientPhone, ClientEmail, IssuedBy) LIKE '%{$parameters->search}%'
            {$departmentQuery} {$categoryQuery} ORDER BY DateCreated asc";
            }
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
    public function SearchPaymentsCount(stdClass $parameters): int
    {

        try {
            $_table = $this->TableName;
            $departmentQuery = ($parameters->department == 99) ? "" : " AND DepartmentId = {$parameters->department}";
            $categoryQuery = ($parameters->category == 99) ? "" : " AND CategoryId = {$parameters->category}";
            $query = "SELECT * FROM $_table where DateCreated >= DATE('{$parameters->from}') AND DateCreated <= DATE('{$parameters->to}')
                AND CONCAT(Amount, ClientName, ClientPhone, ClientEmail, IssuedBy) LIKE '%{$parameters->search}%'
            {$departmentQuery} {$categoryQuery}";
            $dbOptions = array(
                "my_query" => $query,
                "query_action" => "select",
            );
            $dbResult = $this->connectDb->custom_query((object) $dbOptions);
            return $dbResult;

            //

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
        return 0;

    }
    public function GetLastQueriedCSV(stdClass $parameters)
    {

        try {
                $this->load->dbutil();
                $query = "";
                $_table = $this->TableName;
                $delimiter = ",";
                $newline = "\r\n";
                $enclosure = '"';
                $departmentQuery = ($parameters->department == 99) ? "" : " AND DepartmentId = {$parameters->department}";
                $categoryQuery = ($parameters->category == 99) ? "" : " AND CategoryId = {$parameters->category}";

                $query = "SELECT * FROM $_table where DateCreated >= DATE('{$parameters->from}') AND DateCreated <= DATE('{$parameters->to}')
                {$departmentQuery} {$categoryQuery} ORDER BY DateCreated asc";
                $dbResult = $this->db->query($query);

                return $this->dbutil->csv_from_result($dbResult, $delimiter, $newline, $enclosure);

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
        return 0;

    }

}

/* End of file ModelName.php */
