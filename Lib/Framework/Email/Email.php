<?php

namespace Framework\Email;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use stdClass;

class Email
{
    /** @var PHPMailer */
    private $mail;

    /** @var stdClass */
    private $data;

    /** @var Exception */
    private $error;
    
    /** @var Conf.ini */
    private $configurations;

    public function __construct()
    {
        if (file_exists("App/Config/conf.ini")) {
            $this->configurations = parse_ini_file("App/Config/conf.ini", true);
        } else {
            throw new Exception('Arquivo conf.ini não encontrado');
        }

        $this->mail = new PHPMailer(true);
        $this->data = new stdClass();

        $this->mail->isSMTP();
        $this->mail->isHTML();
        $this->mail->setLanguage('br');
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->CharSet = 'utf-8';
        $this->mail->Host = $this->configurations['Email']['email_host'];
        $this->mail->Port = $this->configurations['Email']['email_port'];
        $this->mail->Username = $this->configurations['Email']['email_user'];
        $this->mail->Password = $this->configurations['Email']['email_passwd'];
    }

    
    /**
     * Monta amensagem que será enviada.
     * 
     * @param string $subject Assunto do email.
     * @param string $body Corpo do email.
     * @param string $destiny_name Nome do destinatário. 
     * @param string $destiny_email Email do destinatário.
     * @return Email 
     */
    public function message(string $subject, string $body, string $destiny_name, string $destiny_email) : Email
    {
        $this->data->subject = $subject;
        $this->data->body = $body;
        $this->data->destiny_name = $destiny_name;
        $this->data->destiny_email = $destiny_email;

        return $this;
    }
    
    /**
     * Adiciona anexo ao email.
     * 
     * @param string $filePath 
     * @param string $fileName 
     * @return Email 
     */
    public function attach(string $filePath, string $fileName) : Email
    {
        $this->data->attach[$filePath] = $fileName;

        return $this;
    }
    
    /**
     * Envia o email.
     * 
     * @param string|null $fromName Nome do remetente.
     * @param string|null $fromEmail Email do remetente.
     * @return bool True em caso de sucesso. False em caso de falha.
     */
    public function send(string $fromName = null, string $fromEmail = null) : bool
    {
        if (is_null($fromName)) {
            $fromName = $this->configurations['Email']['email_from_name'];
        }

        if (is_null($fromEmail)) {
            $fromEmail = $this->configurations['Email']['email_from_email'];
        }

        try {
            $this->mail->Subject = $this->data->subject;
            $this->mail->msgHTML($this->data->body);
            $this->mail->addAddress($this->data->destiny_email, $this->data->destiny_name);
            $this->mail->setFrom($fromEmail, $fromName);

            if (!empty($this->data->attach)) {
                foreach ($this->data->attach as $path => $name) {
                    $this->mail->addAttachment($path, $name);        
                }
            }

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            $this->error = $e;
            return false;
        }
    }
    
    /**
     * Captura erros no envio do email.
     *
     * @return Exception
     */
    public function error() : ?Exception
    {
        return $this->error;
    }
}