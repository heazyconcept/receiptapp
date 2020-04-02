<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdminApi extends CI_Controller
{

    public $UserID;
    private $siteOptions;

    public function __construct()
    {
        parent::__construct();
        $this->UserID = $this->utilities->GetSessionId();
        $this->config->load('options', true);
        $this->siteOptions = (object) $this->config->item('options');

    }

    public function AddUser()
    {

        try {
            $this->load->model('users');
            $this->load->library('emailservices');
            $request = (object) $_POST;
            if (!isset($request->UserId) || empty($request->UserId)) {
                $exists = $this->users->CheckExist($request->EmailAddress, $request->PhoneNumber);
                if ($exists) {
                    echo $this->utilities->outputMessage("error", "User already exists");
                    return;
                }
                $password = $this->utilities->generateRandomString(6);
                log_message('error', "password for {$request->EmailAddress} is {$password}");
                $request = $this->utilities->AddPropertyToObJect($request, "Password", $password);
                $request = $this->utilities->AddPropertyToObJect($request, "CreatedBy", $this->UserID);
                unset($request->UserId);
                $modelResponse = $this->users->Insert((array) $request);
                if ($modelResponse > 0) {
                    $mailBody = "<p>you have been created on E-receipt as {$request->Role}. <br>
                 your password is {$password}.</p>
                 <p>You are required to change your password once you login</p>";
                    $this->emailservices->SendGeneralMail($request->EmailAddress, $mailBody, $request->FullName, "New Registration");
                    echo $this->utilities->outputMessage("success", "User registered successfully");
                    return;
                }

            } else {

                $existingData = $this->users->GetByEmail($request->EmailAddress);
                if (!empty((array) $existingData) && $existingData->Id != $request->UserId) {
                    echo $this->utilities->outputMessage("error", "User already exists");
                    return;
                }
                $userUpdate = $this->utilities->AddPropertyToObJect($request, "ModifiedBy", $this->UserID);
                $modelResponse = $this->users->update($userUpdate, $request->UserId);
                if ($modelResponse > 0) {
                    echo $this->utilities->outputMessage("success", "User updated successful");
                    return;
                }

            }

        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;
        }
        echo $this->utilities->outputMessage("error", "your request cannot be processed at this moment. Please try again later");

    }
    public function FetchUsers()
    {
        $this->load->model('users');
        $Result = array();
        $data = array();
        $totalData = 0;
        $totalFiltered = 0;
        try {

            $limit = $this->input->get('length') ?? 0;
            $start = $this->input->get('start') ?? 0;
            $totalData = count($this->users->ListAll(0, 0));
            $totalFiltered = $totalData;
            if (empty($this->input->get('search')['value'])) {
                $Result = $this->users->ListAll($limit, $start);
            } else {
                $search = $this->input->get('search')['value'];
                $Result = $this->users->SearchUser($limit, $start, $search);
                $totalFiltered = $this->users->SearchUserCount($search);
            }

            if (!empty($Result)) {
                foreach ($Result as $obj) {
                    $nestedData['FullName'] = $obj->FullName;
                    $nestedData['EmailAddress'] = $obj->EmailAddress;
                    $nestedData['Role'] = $obj->Role;
                    $nestedData['PhoneNumber'] = $obj->PhoneNumber;
                    $nestedData['Action'] = '<button data-id="' . $obj->Id . '" class="delete-user btn btn-danger btn-block"><i class="icon wb-trash" aria-hidden="true"></i> Delete</button>
                                             <button data-id="' . $obj->Id . '" class="edit-user btn btn-dark btn-block"><i class="icon wb-pencil" aria-hidden="true"></i> Edit</button>';
                    $data[] = $nestedData;

                }

            }

        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
        }
        $json_data = array(
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);

    }
    public function InitiateReceipt()
    {
        try {
            $this->load->model('clients');
            $this->load->model('receipts');
            $this->load->model('receiptCategories');
            $this->load->library("emailservices");
            $this->load->model("departments");
            $request = (object) $_POST;
            $clientObject = $request;
            $clientId = 0;
            if (!isset($request->ReceiptId) || empty($request->ReceiptId)) {
                if (!isset($request->ClientId) || empty($request->ClientId)) {
                    unset($clientObject->Amount);
                    unset($clientObject->Description);
                    unset($clientObject->CategoryId);
                    unset($clientObject->DepartmentId);
                    unset($clientObject->Currency);
                    unset($clientObject->PaymentDetails);
                    unset($clientObject->ModeOfPayment);
                    unset($clientObject->ClientId);
                    unset($clientObject->ReceiptTag);
                    $clientObject = $this->utilities->AddPropertyToObJect($clientObject, "CreatedBy", $this->UserID);
                   
                    $isExists = $this->clients->CheckDuplicate($request->EmailAddress);
                    if ($isExists) {
                        echo $this->utilities->outputMessage("error", "Client already exists");
                        return;
                    }
                    $clientResponse = $this->clients->Insert((array) $clientObject);
                    if ($clientResponse > 0) {
                        $clientId = $clientResponse;
                    } else {
                        echo $this->utilities->outputMessage("fatal");
                        return;
                    }

                } else {
                    $clientId = $request->ClientId;
                }

                $request = (object) $_POST;
                $categoryData = $this->receiptCategories->Get($request->CategoryId);
                $receiptTag ="";
                $lastId = $this->receipts->GetLastInsertedIdByCategory($request->CategoryId);
                $currentId = (int) $lastId + 1;
                $receiptId = $this->utilities->ProcessReceiptNumber($currentId);
                $_clientID = $this->utilities->ProcessClientNumber($clientId);
                $receiptNumber = $this->utilities->GenerateReceipt($categoryData->Abbreviation, $_clientID, $receiptId);
                if ($request->CategoryId == 4) {
                    if (empty($request->ReceiptTag)) {
                       echo $this->utilities->outputMessage("error", "You need to add a Tag to this category");
                       return;
                    }
                    $receiptTag = $request->ReceiptTag;
                }
                $receiptData = array(
                    'ReceiptId' => $receiptNumber,
                    'Amount' => $request->Amount,
                    'Description' => $request->Description,
                    'ClientId' => $clientId,
                    'TransactionState' => 'Pending',
                    'CreatedBy' => $this->UserID,
                    'CategoryId' => $request->CategoryId,
                    'ReceiptNumber' => $receiptId,
                    'PaymentDetails' => $request->PaymentDetails,
                    'ModeOfPayment' => $request->ModeOfPayment,
                    'Currency' => $request->Currency,
                    'DepartmentId' => $request->DepartmentId,
                    'ReceiptTag' => $receiptTag,
                );
                $receiptResponse = $this->receipts->Insert($receiptData);
                if ($receiptResponse > 0) {
                    $departmentEmail = $this->departments->GetDepartmentEmail($request->DepartmentId);
                    $mailBody = "<p>New receipt has been initiated. <br> Kindly review and action appropriately</p>";
                    $this->emailservices->SendGeneralMail($this->siteOptions->admin_email, $mailBody, "Admin", "New Reciept");
                    $this->emailservices->SendGeneralMail($departmentEmail, $mailBody, "Admin", "New Reciept");
                    echo $this->utilities->outputMessage("success", "Receipt initiated successfully");
                    return;
                }
            } else {

                $clientData = $this->clients->Get($request->ClientId);
                $receiptData = $this->receipts->Get($request->ReceiptId);
                if ($this->UserID != $receiptData->CreatedBy) {
                    echo $this->utilities->outputMessage("error", "Only the initiator can edit the receipt");
                    return;
                }
                $request->ReceiptTag = ($request->CategoryId == 4) ?  $request->ReceiptTag : "";
                $modelResponse = $this->receipts->update($request, $request->ReceiptId);
                if ($modelResponse > 0) {
                    $departmentEmail = $this->departments->GetDepartmentEmail($request->DepartmentId);
                    $mailBody = "<p>Receipt number: {$receiptData->ReceiptId} has been modified. <br> Kindly review and action appropriately</p>";
                    $this->emailservices->SendGeneralMail($this->siteOptions->admin_email, $mailBody, "Admin", "Reciept {$receiptData->ReceiptId}");
                    $this->emailservices->SendGeneralMail($departmentEmail, $mailBody, "Admin", "Reciept {$receiptData->ReceiptId}");

                    echo $this->utilities->outputMessage("success", "receipt updated successfully");
                    return;
                }

            }
        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;

        }
        echo $this->utilities->outputMessage("error", "Your request cannot be proccessed at this moment please try again later");
        return;
    }
    public function FetchReceipts()
    {
        $this->load->model('clients');
        $this->load->model('receipts');
        $this->load->model('receiptCategories');
        $this->load->model('users');
        $Result = array();
        $data = array();
        $totalData = 0;
        $totalFiltered = 0;
        $targets = array();
        if ($this->utilities->GetSessionRole() == "Agent") {
            $targets = array('CreatedBy' => $this->utilities->GetSessionId());
        }
        try {

            $limit = $this->input->get('length') ?? 0;
            $start = $this->input->get('start') ?? 0;
            $totalData = count($this->receipts->ListAll(0, 0, $targets));
            $totalFiltered = $totalData;
            if (empty($this->input->get('search')['value'])) {
                $Result = $this->receipts->ListAll($limit, $start, $targets);
            } else {
                $search = $this->input->get('search')['value'];
                $Result = $this->receipts->Searchreceipt($limit, $start, $search, $targets);
                $totalFiltered = $this->receipts->SearchreceiptCount($search, $targets);
            }
            if (!empty($Result)) {

                foreach ($Result as $obj) {
                    $editButton = "";
                    if ($obj->TransactionState == "Pending") {
                        $editButton = '<button data-id="' . $obj->Id . '" class="btn btn-success btn-block edit-receipt"><i class="icon wb-pencil" aria-hidden="true"></i> Edit</button>';
                    }
                    $clientData = $this->clients->Get($obj->ClientId);
                    $categoryData = $this->receiptCategories->Get($obj->CategoryId);
                    $nestedData['Date'] = $this->utilities->formatDate($obj->DateCreated);
                    $nestedData['ReceiptId'] = $obj->ReceiptNumber;
                    $nestedData['ReceiptNumber'] = $obj->ReceiptId;
                    $nestedData['ReceiptCategory'] = $categoryData->Category;
                    $nestedData['FullName'] = $clientData->FullName;
                    $nestedData['EmailAddress'] = $clientData->EmailAddress;
                    $nestedData['IssuedBy'] = $this->users->GetName($obj->CreatedBy);
                    $nestedData['Amount'] = $this->utilities->FormatAmount($obj->Amount, $obj->Currency);
                    $nestedData['PaymentMethod'] = $obj->ModeOfPayment ?? "";
                    $nestedData['Status'] = $obj->TransactionState;
                    $nestedData['Action'] = '<a href="' . base_url("admin/viewreceipt/{$obj->Id}") . '" class="btn btn-dark btn-block"><i class="icon wb-eye" aria-hidden="true"></i> View</a>' . $editButton;
                    $data[] = $nestedData;

                }

            }

        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
        }
        $json_data = array(
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);

    }
    public function FetchReport()
    {
        $this->load->model('paymentReport');
        $Result = array();
        $data = array();
        $totalData = 0;
        $totalFiltered = 0;
        $targets = array();

        try {

            $limit = $this->input->get('length') ?? 0;
            $start = $this->input->get('start') ?? 0;
            $categoryId = $this->input->get('CategoryId');
            $departmentId = $this->input->get('DepartmentId');
            $from = $this->input->get('From');
            $to = $this->input->get('To');
            $parameters = array(
                "category" => $categoryId,
                "department" => $departmentId,
                "from" => $from,
                "to" => $to,
            );
            $foo = array(
                "filterParameters" => $parameters,
            );
            $this->utilities->SetSession($foo);
            $totalData = count($this->paymentReport->ListAll((object) $parameters));
            $totalFiltered = $totalData;
            if (empty($this->input->get('search')['value'])) {
                $parameters["limit"] = $limit;
                $parameters["start"] = $start;
                $Result = $this->paymentReport->ListAll((object) $parameters);
            } else {
                $search = $this->input->get('search')['value'];
                $parameters["limit"] = $limit;
                $parameters["start"] = $start;
                $parameters["search"] = $search;
                $Result = $this->paymentReport->SearchPayments((object) $parameters);
                $totalFiltered = $this->paymentReport->SearchPaymentsCount((object) $parameters);
            }
            if (!empty($Result)) {

                foreach ($Result as $obj) {

                    $nestedData['Date'] = $this->utilities->formatDate($obj->DateCreated);
                    $nestedData['ReceiptNumber'] = $obj->ReceiptId;
                    $nestedData['Category'] = $obj->Category;
                    $nestedData['Department'] = $obj->Department;
                    $nestedData['ClientName'] = $obj->ClientName;
                    $nestedData['AmountPaid'] = $obj->Amount;
                    $nestedData['Currency'] = $obj->Currency;
                    $nestedData['IssuedBy'] = $obj->IssuedBy;
                    $data[] = $nestedData;

                }

            }

        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
        }
        $json_data = array(
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);

    }
    public function GetReportCSV()
    {
        try {
            $this->load->model('paymentReport');
            $filterParameters = $this->utilities->GetSession("filterParameters");
            $result = $this->paymentReport->GetLastQueriedCSV((object) $filterParameters);
            echo $this->utilities->outputMessage("success", $result);
            return;
        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;

        }
        echo $this->utilities->outputMessage("error", "Your request cannot be proccessed at this moment please try again later");
        return;
    }

    public function FetchClients()
    {
        $this->load->model('clients');
        $Result = array();
        $data = array();
        $totalData = 0;
        $totalFiltered = 0;
        $targets = array();
        if ($this->utilities->GetSessionRole() == "Agent") {
            $targets = array('CreatedBy' => $this->utilities->GetSessionId());
        }
        try {

            $limit = $this->input->get('length') ?? 0;
            $start = $this->input->get('start') ?? 0;
            $totalData = count($this->clients->ListAll(0, 0, $targets));
            $totalFiltered = $totalData;
            if (empty($this->input->get('search')['value'])) {
                $Result = $this->clients->ListAll($limit, $start, $targets);
            } else {
                $search = $this->input->get('search')['value'];
                $Result = $this->clients->Searchclient($limit, $start, $search, $targets);
                $totalFiltered = $this->clients->SearchclientCount($search, $targets);
            }
            if (!empty($Result)) {

                foreach ($Result as $obj) {
                    $nestedData['ClientNumber'] = $this->utilities->ProcessClientNumber($obj->Id);
                    $nestedData['FullName'] = $obj->FullName;
                    $nestedData['Company'] = $obj->Company;
                    $nestedData['EmailAddress'] = $obj->EmailAddress;
                    $nestedData['PhoneNumber'] = $obj->PhoneNumber;
                    $nestedData['Address'] = $obj->ResidentialAddress;
                    $nestedData['Action'] = '<button data-id="' . $obj->Id . '" class="edit-client btn btn-dark"><i class="icon wb-pencil" aria-hidden="true"></i> Edit</button>';
                    $data[] = $nestedData;

                }

            }

        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
        }
        $json_data = array(
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);

    }
    public function ApproveReceipt($receiptId)
    {
        try {
            $this->load->model('receipts');
            $this->load->model('clients');
            $this->load->library('emailservices');
            $this->load->model('departments');
            $receiptData = $this->receipts->Get((int) $receiptId);
            $clientData = $this->clients->Get($receiptData->ClientId);
            if (!empty((array) $receiptData)) {
                $modelResponse = $this->receipts->ApproveReceipt($receiptData->Id);
                if ($modelResponse > 0) {
                    $receiptData = $this->receipts->Get((int) $receiptId);
                    $departmentEmail = $this->departments->GetDepartmentEmail($receiptData->DepartmentId);
                    $this->emailservices->processReceiptHtml($clientData, $receiptData);
                    $mailMessage = $this->emailservices->GetEmailTemplate($receiptId);
                    $this->emailservices->SendDynamicMail($clientData->EmailAddress, $mailMessage, "Transaction Receipt");
                    $this->emailservices->SendDynamicMail($departmentEmail, $mailMessage, "Transaction Receipt");
                    if ($this->utilities->GetSessionRole() == "Agent") {
                        $agentEmail = $this->utilities->GetSessionEmail();
                        $this->emailservices->SendDynamicMail($agentEmail, $mailMessage, "Transaction Receipt");
                    }
                    echo $this->utilities->outputMessage("success", "receipt approved. Email sent");
                    return;
                }
            }
        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;

        }
        echo $this->utilities->outputMessage("error", "Your request cannot be proccessed at this moment please try again later");
        return;
    }
    public function DeclineReceipt()
    {
        try {
            $this->load->model('receipts');
            $this->load->library('emailservices');
            $this->load->model("users");
            $request = (object) $_POST;
            $receiptData = $this->receipts->Get($request->ReceiptId);
            $modelResponse = $this->receipts->DeclineReceipt($receiptData->Id, $request->RejectReason);
            if ($modelResponse > 0) {
                $initiatorData = $this->users->Get($receiptData->CreatedBy);
                $mailBody = "<p>The receipt - {$request->ReceiptId} you initiated has been declined with the following reason: <br> {$request->RejectReason}</p>
                <p>If you think this is a mistake, kindly contact the admin</p>";
                $this->emailservices->SendGeneralMail($initiatorData->EmailAddress, $mailBody, $initiatorData->FullName, "Transaction Declined");
                echo $this->utilities->outputMessage("success", "transaction declined");
                return;
            }

        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;

        }
        echo $this->utilities->outputMessage("error", "Your request cannot be proccessed at this moment please try again later");
        return;
    }
    public function AddClient()
    {
        try {
            $this->load->model("clients");
            $request = (object) $_POST;
            if (isset($request->ClientId) && !empty($request->ClientId)) {
                $ClientUpdate = $this->utilities->AddPropertyToObJect($request, "ModifiedBy", $this->UserID);
                $modelResponse = $this->clients->Update($ClientUpdate, $ClientUpdate->ClientId);
                if ($modelResponse > 0) {
                    echo $this->utilities->outputMessage("success", "Client updated Successfully");
                    return;
                }
            } else {
                $isExists = $this->clients->CheckDuplicate($request->EmailAddress, $request->PhoneNumber);
                if ($isExists) {
                    echo $this->utilities->outputMessage("error", "Client already exists");
                    return;
                }
                $clientData = $this->utilities->AddPropertyToObJect($request, "CreatedBy", $this->UserID);
                unset($clientData->ClientId);
                $modelResponse = $this->clients->Insert((array) $clientData);
                if ($modelResponse > 0) {
                    echo $this->utilities->outputMessage("success", "Client Added Successfully");
                    return;
                }

            }

        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;

        }
        echo $this->utilities->outputMessage("error", "Your request cannot be proccessed at this moment please try again later");
        return;
    }
    public function AddDepartment()
    {
        try {
            $this->load->model("departments");
            $request = (object) $_POST;
            if (isset($request->DepartmentId) && !empty($request->DepartmentId)) {
                $DepartmentUpdate = $this->utilities->AddPropertyToObJect($request, "ModifiedBy", $this->UserID);
                $modelResponse = $this->departments->update($DepartmentUpdate, $request->DepartmentId);
                if ($modelResponse > 0) {
                    echo $this->utilities->outputMessage("success", "Department updated successfully");
                    return;

                }

            } else {
                $DepartmentData = $this->utilities->AddPropertyToObJect($request, "CreatedBy", $this->UserID);
                unset($DepartmentData->DepartmentId);
                $modelResponse = $this->departments->Insert((array) $DepartmentData);
                if ($modelResponse > 0) {
                    echo $this->utilities->outputMessage("success", "Department added successfully");
                    return;
                }
            }

        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;

        }
        echo $this->utilities->outputMessage("error", "Your request cannot be proccessed at this moment please try again later");
        return;
    }
    public function DeleteUser($userId)
    {
        try {
            $this->load->model("users");
            $isDeleted = $this->users->DeleteUser($userId);
            if ($isDeleted) {
                echo $this->utilities->outputMessage("success", "User successfully deleted");
                return;
            }
        } catch (\Throwable $th) {
            $this->utilities->LogError($th);
            echo $this->utilities->outputMessage("fatal");
            return;

        }
        echo $this->utilities->outputMessage("error", "Your request cannot be proccessed at this moment please try again later");
        return;
    }
    public function SearchClient($searchParam = "")
    {
        // echo $searchParam; die();
        $this->load->model("clients");
        if ($this->utilities->IsNullOrEmptyString($searchParam)) {
            echo "";
            return;
        }
        $searchData = $this->clients->Searchclient(5, 0, $searchParam);
        if (!empty($searchData)) {
            $output = array();
            foreach ($searchData as $item) {
                $output[] = array(
                    "ClientId" => $item->Id,
                    "ClientName" => $item->FullName,
                    "ClientDetails" => "{$item->EmailAddress} - {$item->PhoneNumber}",
                );
            }
            echo json_encode($output);
            return;

        }
        echo "";
        return;

    }
    public function SearchTag($searchParam = "")
    {
        $this->load->model("receipts");
        if ($this->utilities->IsNullOrEmptyString($searchParam)) {
            echo "";
            return;
        }
        $searchData = $this->receipts->SearchTag($searchParam);
        if (!empty($searchData)) {
            echo json_encode($searchData);
            return;

        }
        echo "";
        return;
    }
    public function GetClient($clientId)
    {
        $this->load->model("clients");
        $clientdata = $this->clients->Get($clientId);
        echo json_encode($clientdata);
        return;

    }
    public function GetUser($userId)
    {
        $this->load->model("users");
        $userdata = $this->users->Get($userId);
        echo json_encode($userdata);
        return;
    }
    public function GetReceipt($receiptId)
    {
        $this->load->model("clients");
        $this->load->model("receipts");
        $receiptData = $this->receipts->Get($receiptId);
        $clientdata = $this->clients->Get($receiptData->ClientId);

        // log_message('error', serialize($clientdata));

        $response = array(
            "CategoryId" => $receiptData->CategoryId,
            "DepartmentId" => $receiptData->DepartmentId,
            "FullName" => $clientdata->FullName,
            "EmailAddress" => $clientdata->EmailAddress,
            "PhoneNumber" => $clientdata->PhoneNumber,
            "ResidentialAddress" => $clientdata->ResidentialAddress,
            "Amount" => $receiptData->Amount,
            "Currency" => $receiptData->Currency,
            "PaymentDetails" => $receiptData->PaymentDetails,
            "ModeOfPayment" => $receiptData->ModeOfPayment,
            "Description" => $receiptData->Description,
            "ClientId" => $receiptData->ClientId,
            "ReceiptId" => $receiptData->Id,
            "ReceiptTag" => $receiptData->ReceiptTag,
        );

        echo json_encode($response);
        return;
    }
    public function GetDepartment($departmentId)
    {
        $this->load->model("departments");
        $departmentdata = $this->departments->Get($departmentId);
        echo json_encode($departmentdata);
        return;
    }

}

/* End of file AdminApi.php */
