<?php

return call_user_func(function() {
  /** @todo: Fazer o log funcionar */
  $logger = call_user_func(function() {
    $settings = require __DIR__ . '/../config/settings.php';
    $settings = $settings['settings']['logger'];

    $logger = new Monolog\Logger($settings['name']);
    // $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
    // $logger->pushFormatter(new Monolog\Formatter\LineFormatter());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));

    return $logger;
  });

  $sendMailSMTP = function(StdClass $transmitter, $senderMail, $senderName, $subject, $messageContent, $receiverMail) use ($logger) {
    $mail = new PHPMailer;


    /** Debug output level. Options:
      * 0: No output
      * 1: Commands
      * 2: Data and commands
      * 3: As 2 plus connection status
      * 4: Low-level data output
    */
    $mail->SMTPDebug = 0; // Enable debug output
    /** The default language is English. */
    $mail->setLanguage('pt_br'); // Translations for PHPMailer error messages

    // Set SMTP
    if ( $transmitter->smtp->active ) {
      $mail->isSMTP();                                // Set mailer to use SMTP
      $mail->Host     = $transmitter->smtp->host;     // Specify main and backup SMTP servers
      $mail->Port     = $transmitter->smtp->port;     // TCP port to connect to
      $mail->SMTPAuth = true;                         // Enable SMTP authentication
      $mail->Username = $transmitter->email;          // SMTP username
      $mail->Password = $transmitter->smtp->password; // SMTP password

      // Enable TLS encryption, `ssl` also accepted (if needed)
      if ( ! empty($transmitter->smtp->criptografy) ) {
        $mail->SMTPSecure = $transmitter->smtp->criptografy;
      }
    }

    $mail->setFrom($transmitter->email, $senderName);
    $mail->addAddress($receiverMail);            // Add a recipient (name is optional)
    $mail->addReplyTo($senderMail, $senderName); // Add a recipient to reply
    // $mail->addCC('cc@example.com');              // Add copy
    // $mail->addBCC('bcc@example.com');            // Add hidden copy

    // $mail->addAttachment('/var/tmp/file.tar.gz');      // Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name

    $mail->isHTML(true); // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $messageContent;
    $mail->CharSet = 'UTF-8';

    // Format message to raw text
    $textMessage   = str_replace('<br>', '\n', $messageContent);
    $textMessage   = str_replace('<br />', '\n', $textMessage);
    $mail->AltBody = strip_tags($textMessage); // Add raw text message to alternative body

    // Send message and return success or fail (true/false)
    return $mail->send();
  };

  return $sendMailSMTP;
});