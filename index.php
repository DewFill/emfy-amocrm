<?php
require "vendor/autoload.php";

use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Models\NoteType\CommonNote;
use Opravdin\AmoHook\AmoHook;
use Opravdin\AmoHook\Entities;
use Opravdin\AmoHook\Events;


set_exception_handler(function ($exception) {
    error_log("ERROR: " . $exception->getMessage() . "\n\nLINE: ");
    die();
});


$env = parse_ini_file('.env');
if (empty($env["ACCESS_TOKEN"])) {
    die("'ACCESS_TOKEN' is not set in .env file'");
}
$accessToken = $env["ACCESS_TOKEN"];


$apiClient = new \AmoCRM\Client\AmoCRMApiClient();
$longLivedAccessToken = new LongLivedAccessToken($accessToken);
$apiClient->setAccessToken($longLivedAccessToken)
    ->setAccountBaseDomain('meamoartemynet.amocrm.ru');


$hook = AmoHook::build($_POST)
    # Хук на добавление контактов/лидов
    ->register(
        [Entities::CONTACT, Entities::LEAD],
        [Events::ADD],
        function ($payload) use ($apiClient) {

            if ($payload["entity"] === Entities::LEAD) {
                $entity = $apiClient->leads();
            } else {
                $entity = $apiClient->contacts();
            }

            $entity = $entity->getOne($payload["data"]["id"]);

            $responsibleUser = $apiClient->users()->getOne($entity->getResponsibleUserId());

            $commonNote = (new CommonNote())
                ->setEntityId($entity->getId())
                ->setText(
                    "Название: {$entity->getName()}" .
                    "\nВремя добавления: " . date('Y-m-d h:i:s', $entity->getCreatedAt()));

            $apiClient->notes($payload["entity"])->addOne($commonNote);
        })

    # Хук на обновление контактов/лидов
    ->register(
        [Entities::CONTACT, Entities::LEAD],
        [Events::UPDATE],
        function ($payload) use ($apiClient) {
            log_tg($payload);
            if ($payload["entity"] === Entities::LEAD) {
                $entity = $apiClient->leads();
            } else {
                $entity = $apiClient->contacts();
            }

            log_tg($apiClient->leads()->getLastRequestInfo());
            $entity = $entity->getOne($payload["data"]["id"]);

            $commonNote = (new CommonNote())
                ->setEntityId($entity->getId())
                ->setText(
                    "Название: {$entity->getName()}" .
                    "\nВремя изменения: " . date('Y-m-d h:i:s', $entity->getUpdatedAt()));

            $apiClient->notes($payload["entity"])->addOne($commonNote);
        });


$hook->handle();