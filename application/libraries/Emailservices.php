<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Emailservices
{
    protected $ci;
    public $emailTemplate;
    public $primaryLogo;
    public $secondaryLogo;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('html2pdf');
        $this->ci->load->model('users');
        $this->ci->load->model('receiptCategories');
        $this->secondaryLogo = asset_url("images/logoreceipt.png");
        $this->primaryLogo = asset_url("images/logonew.png");
    }
    public function processReceiptHtml(stdClass $clientData, stdClass $receiptData): string
    {
        try {
            $issuedBy = $this->ci->users->GetName($receiptData->CreatedBy);
            $status = "";
            $receiptLogo = $this->primaryLogo;
            $abbreviation = $this->ci->receiptCategories->GetAbbreviation($receiptData->CategoryId);
            if ($receiptData->TransactionState == "Approved") {
                $status = asset_url("images/paid.png");
            } elseif ($receiptData->TransactionState == "Pending") {
                $status = asset_url("images/pending.jpg");
            }else{
                
                $status = asset_url("images/declined.png");
            }
            if ($abbreviation == "eICF") {
                $receiptLogo = $this->secondaryLogo;
            }
            $actionReplace = array(
                '{{ReceiptNumber}}',
                '{{Name}}',
                '{{Date}}',
                '{{Company}}',
                '{{Address}}',
                '{{Description}}',
                '{{Amount}}',
                '{{PaymentMethod}}',
                '{{IssuedBy}}',
                '{{Status}}',
                '{{PdfUrl}}',
                '{{PhoneNumber}}',
                '{{PaymentDetails}}',
                '{{CheckImage}}',
                '{{ReceiptLogo}}',
            );
            $actionWith = array(
                $receiptData->ReceiptId,
                $clientData->FullName,
                $this->ci->utilities->formatDate($receiptData->DateCreated),
                $clientData->Company ?? "",
                $clientData->ResidentialAddress,
                $receiptData->Description,
                $this->ci->utilities->FormatAmount($receiptData->Amount, $receiptData->Currency),
                $receiptData->ModeOfPayment,
                $issuedBy,
                $status,
                $this->proceessReceiptPdf($clientData, $receiptData),
                $clientData->PhoneNumber,
                $receiptData->PaymentDetails,
                asset_url('images/checkmark.jpg'),
                $receiptLogo,
            );
            $actionTemplate = file_get_contents('maitemplate/receipttemplate.html', true);
            $mailString = str_replace($actionReplace, $actionWith, $actionTemplate);
            $this->SetEmailTemplate($mailString, $receiptData->Id);
        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
        return "";
    }
    public function proceessReceiptPdf(stdClass $clientData, stdClass $receiptData)
    {
        try {
            $issuedBy = $this->ci->users->GetName($receiptData->CreatedBy);
            $status = "";
            $receiptLogo = $this->primaryLogo;
            $abbreviation = $this->ci->receiptCategories->GetAbbreviation($receiptData->CategoryId);
            if ($receiptData->TransactionState == "Approved") {
                $status = asset_url("images/paid.png");
            } elseif ($receiptData->TransactionState == "Pending") {
                $status = asset_url("images/pending.jpg");
            }else{
                $status = asset_url("images/declined.png");
            }
            if ($abbreviation == "eICF") {
                $receiptLogo = $this->secondaryLogo;
            }
            $actionReplace = array(
                '{{ReceiptNumber}}',
                '{{Name}}',
                '{{Date}}',
                '{{Company}}',
                '{{Address}}',
                '{{Description}}',
                '{{Amount}}',
                '{{PaymentMethod}}',
                '{{Status}}',
                '{{IssuedBy}}',
                '{{PhoneNumber}}',
                '{{PaymentDetails}}',
                '{{CheckImage}}',
                '{{ReceiptLogo}}',
            );
            // $this->load->library('utilities');
            $actionWith = array(
                $receiptData->ReceiptId,
                $clientData->FullName,
                $this->ci->utilities->formatDate($receiptData->DateCreated),
                $clientData->Company ?? "",
                $clientData->ResidentialAddress,
                $receiptData->Description,
                $this->ci->utilities->FormatAmount($receiptData->Amount, $receiptData->Currency),
                $receiptData->ModeOfPayment,
                $status,
                $issuedBy,
                $clientData->PhoneNumber,
                $receiptData->PaymentDetails,
                asset_url('images/checkmark.jpg'),
                $receiptLogo,
            );
            $actionTemplate = file_get_contents('maitemplate/receipttemplatepdf.html', true);
            $mailString = str_replace($actionReplace, $actionWith, $actionTemplate);
            $this->ci->html2pdf->folder('./assets/pdfs/');
            $fileName = "receipt-" . $abbreviation . "-" . $receiptData->ReceiptNumber . '.pdf';

            //Set the filename to save/download as
            $this->ci->html2pdf->filename($fileName);

            //Set the paper defaults
            $this->ci->html2pdf->paper('a4', 'portrait');

            //Load html view
            $this->ci->html2pdf->html($mailString);

            if ($this->ci->html2pdf->create('save')) {
                return asset_url("pdfs/{$fileName}");
            }

        } catch (\Throwable $th) {
            log_message('error', $th->getMessage());
        }
        return "#";

    }
    public function SetEmailTemplate(string $template, string $receiptId)
    {
        $emailData = array(
            $receiptId => $template,
        );
        $this->emailTemplate = $emailData;
    }
    public function GetEmailTemplate(string $receiptId): string
    {
        return $this->emailTemplate[$receiptId] ?? "";
    }

    public function SendGeneralMail(string $to, string $message, string $name, string $subject)
    {
        // $userData = $this->ci->users->Get($userId);
        $sourceMail = "no-reply@mapleeducation.ca";
        $sourceName = 'E-Receipts';
        $subject = $subject;
        $actionReplace = array(
            '{{customerName}}',
            '{{mailBody}}',
        );
        $actionWith = array(
            $name,
            $message,
        );
        $actionTemplate = file_get_contents('maitemplate/general.html', true);
        $mailString = str_replace($actionReplace, $actionWith, $actionTemplate);
        $this->SendMail($sourceMail, $sourceName, $to, $subject, $mailString);

    }
    public function SendDynamicMail(string $to, string $message, string $subject)
    {
        $sourceMail = "no-reply@mapleeducation.ca";
        $sourceName = 'E-receipt';
        $subject = $subject;
        $this->SendMail($sourceMail, $sourceName, $to, $subject, $message);
    }

    private function SendMail(string $fromEmail, string $fromName, string $toEmail, string $subject, string $mailBody)
    {
        $url = 'http://mailapi.osigla.com.ng/mailService/sendMail';

        //create a new cURL resource
        $ch = curl_init($url);
        //setup request to send json via POST
        $data = array(
            'fromEmail' => $fromEmail,
            'fromName' => $fromName,
            'toEmail' => $toEmail,
            'subject' => $subject,
            'mailBody' => $mailBody,
        );
        $payload = json_encode($data);

        //attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        //set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        //return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute the POST request
        $result = curl_exec($ch);

        log_message('error', $result);

        //close cURL resource
        curl_close($ch);

    }

}

/* End of file EmailServices.php */
