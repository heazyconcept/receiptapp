<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Utilities
{
    protected $ci;
    public $pageTitle;

    public function __construct()
    {
        $this->ci = &get_instance();
    }
    public function formatDate($date)
    {
        return date("F j, Y g:i a", strtotime($date));
    }
    public function DateFormat($date)
    {
        return date("F j, Y", strtotime($date));
    }
    public function DbTimeFormat()
    {
        return date("Y-m-d H:i:s");

    }
    public function DbDateFormat()
    {
        return date("Y-m-d ");

    }
    public function Currency()
    {
        return "₦";
    }
    public function outputMessage($type, $message = "", $redirectURL = "")
    {
        if ($type == "success") {
            $output = array(
                "StatusCode" => "00",
                "StatusMessage" => $message,
                "RedirectUrl" => $redirectURL,
            );
        } elseif ($type == "error") {
            $output = array(
                "StatusCode" => "99",
                "StatusMessage" => $message,
            );
        } elseif ($type == "fatal") {
            $output = array(
                "StatusCode" => "99",
                "StatusMessage" => 'Internal server error',
            );
        }
        return json_encode($output);

    }
    public function UpdateRequestId($reqId)
    {
        $requestId = $this->GenerateUniqueNumber() . $reqId;
        $processData = array(
            "RequestId" => $requestId,
        );
        $dbOption = array(
            "table_name" => "orderrequest",
            "process_data" => $processData,
            "targets" => array("Id" => $reqId),
        );
        $this->ci->connectDb->modify_data(json_encode($dbOption));

    }
    public function SetSession($data)
    {
        $this->ci->session->set_userdata($data);

    }
    public function PrepareUserSession(stdClass $UserData): array
    {
        $UserSession = array(
            "UserId" => $UserData->Id,
            "FullName" => $UserData->FullName,
            "EmailAddress" => $UserData->EmailAddress,
            "PhoneNumber" => $UserData->PhoneNumber,
            "Role" => $UserData->Role,
            "IsPasswordChanged" => $UserData->IsPasswordChanged,
        );
        return $UserSession;

    }
    public function GetSessionRole()
    {
        return $this->ci->session->userdata('Role') ?? "";
    }
    public function GetSession($key)
    {
        return $this->ci->session->userdata($key) ?? "";
    }
     public function GetPasswordStatus()
    {
        return $this->ci->session->userdata('IsPasswordChanged') ?? "";
    }
    public function GetSessionId()
    {
        return $this->ci->session->userdata('UserId') ?? "";
    }
     public function GetSessionEmail()
    {
        return $this->ci->session->userdata('EmailAddress') ?? "";
    }
    public function GetCountries(): string
    {
        $Countries = file_get_contents(base_url("json/countries.json"));
        return $Countries;
    }
    public function GeneratePassword(string $Password): string
    {
        $token = md5($Password);
        $EncryptedPassword = hash("sha512", $token . $Password);
        return $EncryptedPassword;
    }
    public function URLHash($string)
    {
        return md5($string);
    }
    public function IsNullOrEmptyString($str): bool
    {
        return (!isset($str) || trim($str) === '' || empty($str));
    }
    public function KeepPresentState(string $URL)
    {
        $this->ci->session->set_userdata("TempURl", $URL);
    }
    public function GetPresentState(): string
    {
        if ($this->ci->session->has_userdata("TempURl")) {
            $PresentState = $this->ci->session->userdata("TempURl");
            $this->DestroyPresentState();
            return $PresentState;

        }
        return "";
    }
    private function DestroyPresentState()
    {
        $this->ci->session->unset_userdata('TempURl');
    }
    public function GenerateGUID(): string
    {
        if (function_exists('com_create_guid')) {
            $Guid = com_create_guid();
            $Guid = str_replace('-', '', $Guid);
            $Guid = str_replace('{', '', $Guid);
            $Guid = str_replace('}', '', $Guid);
            return strtolower($Guid);
        } else {
            mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $uuid = substr($charid, 0, 8)
            . substr($charid, 8, 4)
            . substr($charid, 12, 4)
            . substr($charid, 16, 4)
            . substr($charid, 20, 12);

            return $uuid;
        }

    }
    public function AddPropertyToObJect(stdClass $Object, string $Key, $Value): stdClass
    {
        $ObjectArray = (array) $Object;
        $ObjectArray[$Key] = $Value;
        return (object) $ObjectArray;
    }
    private function GenerateUniqueNumber()
    {
        return abs(crc32(uniqid()));

    }
    public function ProcessTable($DataOption): string
    {
        try {
            $json_data = array(
                "draw" => $DataOption->Draw,
                "recordsTotal" => $DataOption->totalData,
                "recordsFiltered" => $DataOption->totalFiltered,
                "data" => $DataOption->Data,
            );
            return json_encode($json_data);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
        return "";

    }
    public function UploadFile(string $UploadName, string $UploadFolder): string
    {
        try {
            $this->ci->load->helper('form');
            $config['upload_path'] = './upload/' . $UploadFolder . '/';
            $config['allowed_types'] = 'jpg|png|gif|';
            $config['max_size'] = 10000;
            $this->ci->load->library('upload', $config);
            if (!$this->ci->upload->do_upload($UploadName)) {
                $error = array('error' => $this->upload->display_errors());
                $this->LogError($error);
                return '';

            } else {
                $data = $this->ci->upload->data();
                $uploadPath = 'upload/' . $UploadFolder . '/' . $data['file_name'];
                return base_url($uploadPath);

            }
        } catch (\Throwable $th) {
            $this->LogError($th);

        }
        return '';
    }
    public function LogError($Error)
    {
        log_message('error', $Error->getMessage());
    }
    public function CountDays($ExpiryDate): int
    {
        $today = $this->DbTimeFormat();
        $diff = strtotime($ExpiryDate) - strtotime($today);
        return round($diff / 86400) + 1;
    }
    public function GetProfileImage($UserImage): string
    {
        $imageUrl = (empty($UserImage)) ?
        'https://via.placeholder.com/500x500' :
        base_url('upload/profile_pic/' . $UserImage);
        return $imageUrl;
    }
    public function FormatAmount(float $amount, string $currency)
    {
        return $currency . number_format($amount, 2);
    }
    public function CreateSlug(string $String): string
    {
        $slug = str_replace(" ", "-", "$String");
        $slug = str_replace("--", "-", "$slug");
        $slug = trim($slug);
        $slug = rtrim($slug, "-");
        return strtolower($slug);
    }
    public function CreatePagination($configuration)
    {
        $this->ci->load->library('pagination');
        $config['base_url'] = "#";
        $config['total_rows'] = $configuration->totalRows;
        $config['per_page'] = $configuration->perPage;
        $config['uri_segment'] = 3;
        $config['num_links'] = 3;
        $config['full_tag_open'] = '<ul class="pagination pagination-primary m-b-0">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li class="page-item active">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '<i class="zmdi zmdi-arrow-right"></i>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="zmdi zmdi-arrow-left"></i>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"> <a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->ci->pagination->initialize($config);

        return $this->ci->pagination->create_links();

    }
    public function AddToCart(stdClass $ProductData)
    {
        try {
            $this->ci->load->library("cart");
            $CartData = array(
                'id' => $ProductData->Id,
                'name' => $this->CleanString($ProductData->ProductName),
                'qty' => $ProductData->Quantity,
                'price' => $ProductData->ProductAmount,
                'options' => array(
                    'images' => $ProductData->Image,
                    'wallet_amount' => $ProductData->WalletAmount,
                    'order_id' => $this->GenerateUniqueNumber(),
                ),
            );
            // return $CartData;
            return $this->ci->cart->insert($CartData);
        } catch (\Throwable $th) {
            $this->LogError($th);
            return 0;
        }

    }
    public function ViewCart(): array
    {
        try {
            $this->ci->load->library("cart");
            return $this->ci->cart->contents();
        } catch (\Throwable $th) {
            LogError($th);
            return array();
        }

    }
    public function GetCartTotal($type = '')
    {
        $this->ci->load->library("cart");
        if (empty($type)) {
            return $this->FormatAmount($this->ci->cart->total());
        } elseif ($type == 'raw') {
            return $this->ci->cart->total();
        } else {
            return '';
        }

    }
    public function UpdateCart($rowId, $quantity)
    {
        try {
            $this->ci->load->library("cart");
            $CartUpdate = array(
                "rowid" => $rowId,
                "qty" => $quantity,
            );
            $this->ci->cart->update($CartUpdate);
            return true;

        } catch (\Throwable $th) {
            $this->LogError($th);
            return false;
        }

    }
    public function RemoveCartItem($rowId): bool
    {
        try {
            $this->ci->load->library("cart");
            $CartUpdate = array(
                "rowid" => $rowId,
                "qty" => 0,
            );
            $this->ci->cart->update($CartUpdate);
            return true;

        } catch (\Throwable $th) {
            $this->LogError($th);
            return false;
        }

    }
    public function ClearCart(): bool
    {
        try {
            $this->ci->load->library("cart");

            $this->ci->cart->destroy();
            return true;

        } catch (\Throwable $th) {
            $this->LogError($th);
            return false;
        }

    }
    public function CleanString(string $string): string
    {
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string);
    }
    public function CalculatePercentage($productAmount, $walletAmount)
    {
        $totalAmount = $productAmount + $walletAmount;
        $percentage = ($walletAmount / $totalAmount) * 100;
        return round($percentage) . '%';
    }
    public function GetTotalWalletAmount()
    {
        $cartContent = $this->ViewCart();
        $totalAmount = 0;
        if (!empty($cartContent)) {
            foreach ($cartContent as $cart) {
                $totalAmount += $cart['options']['wallet_amount'];
            }

        }
        return $totalAmount;
    }
    public function SetPageTitle($pageTile = "")
    {
        $this->pageTitle = $pageTile;
    }
    public function GetPageTitle()
    {
        return $this->pageTitle;
    }
    public function CalculateDistance(stdClass $startingCoordinates, stdClass $destinationCoordinates, string $unit = 'k')
    {

        $LongitudeDiff = $startingCoordinates->Longitude - $destinationCoordinates->Longitude;
        $distance = sin(deg2rad($startingCoordinates->Latitude)) *
        sin(deg2rad($destinationCoordinates->Latitude)) +
        cos(deg2rad($startingCoordinates->Latitude)) *
        cos(deg2rad($destinationCoordinates->Latitude)) *
        cos(deg2rad($LongitudeDiff));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $miles = $distance * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else if ($unit == "M") {
            return $miles;
        }
        return 0;
    }
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function GenerateReceipt(string $abbreiviation, string $ClientId, string $receiptId):string
    {
        try {
            $thisYear = date("Y");
            $thisMonth = date("m");
            $receiptNumber = "{$abbreiviation}/{$thisYear}/{$thisMonth}/{$ClientId}/{$receiptId}";
            return $receiptNumber;
        } catch (\Throwable $th) {
            $this->LogError($th);
        }
        return "";
    }
    public function ProcessReceiptNumber($receiptId):string
    {
       return str_pad($receiptId,  4, "000",STR_PAD_LEFT);
        
    }
    public function ProcessClientNumber($ClientId):string
    {
       return str_pad($ClientId,  3, "00",STR_PAD_LEFT);
    }


}

/* End of file LibraryName.php */
;
