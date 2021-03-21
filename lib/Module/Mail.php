<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Module;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    const DESTINATAIRE_MAIL = [
        'destinataire@mail.fr'
    ];

    protected $error;
    protected $twig;

    public function loadTwig()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../template/');
        $this->twig = new \Twig_Environment($loader, [
            'cache' => 'cache/twig-cache',
            'debug' => true
        ]);
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }

    public function send(string $sujet = '', string $content = '')
    {
        $phpMailer = new PHPMailer();

        try {

            //Server settings
            // $phpMailer->SMTPDebug = SMTP::DEBUG_SERVER;
            $phpMailer->isSMTP();
            $phpMailer->Host       = 'smtp.gmail.com';
            $phpMailer->SMTPAuth   = true;
            $phpMailer->Username   = 'ID';
            $phpMailer->Password   = 'PASSWORD';
            $phpMailer->Port       = 587;
            $phpMailer->CharSet = 'UTF-8';

            $phpMailer->setFrom('lci@currentdomain.com', 'LCI Packaging');

            foreach (self::DESTINATAIRE_MAIL as $destinataire) {
                $phpMailer->addAddress($destinataire); 
            }

            // Content
            $phpMailer->isHTML(true);
            $phpMailer->Subject = $sujet;
            $phpMailer->Body    = $content;
            $phpMailer->AltBody = $content;

            $phpMailer->send();

        } catch (Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
        // return mail($destinataire, $sujet, $content, $headers);
    }

    public function sendEmail(string $sujet, array $array)
    {
        $this->loadTwig();

        $content = $this->twig->render('email/contact.html.twig', [
            'title' => $sujet,
            'data' => $array,
            'server' => $_SERVER
        ]);

        return $this->send($sujet, $content);
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): Mail
    {
        $this->error = $error;

        return $this;
    }
}
