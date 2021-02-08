<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = 'eb0f21bbf8f10d68dc71aee4a93a6c3e';
    private $api_key_secret = 'e71bee886889322bb2c97e8229ca1fcb';

    public function send($to_email, $to_name, $subject, $content)
    {

        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "laboutiquedanso@gmail.com",
                        'Name' => "La Boutique d'Anso"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' =>   $to_name
                        ]
                    ],
                    'TemplateID' => 2381447,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,

                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && ($response->getData());
    }
}