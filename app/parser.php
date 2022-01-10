<?php

use Goutte\Client;
use ResultParser\AuxParser;

require '../vendor/autoload.php';

$schedules = array(
    "Vereinsnummer" => "10311",
    "VereinsId" => "1343",
    "notification" => "thomas.dascoli@gmail.com",
    "schedules" => array(
        "default" => array(
            "url" => "http://www.football.ch/portaldata/1/nisrd/WebService/verein/calendar.asmx/Verein?v=1343&away=1&sp=de&format=csv",
            "table" => "spielplan",
            "custom" => false
        ),
        "custom" => array(
            "url" => "custom.csv",
            "table" => "spielplan_custom",
            "custom" => true
        )
    )
);

/*
$parser = new ScheduleParser(__DIR__);

$parser->parse($schedules);
*/

const games = array(
    "703247",
    "702677",
    "703285",
    "111671",
    "512555"
);

function endsWith(string $haystack, array $needles) : bool {
    $endsWith = false;
    foreach ($needles as $needle){
        if (str_ends_with($haystack, $needle)){
            $endsWith = true;
        }
    }
    return $endsWith;
}

$client = new Client();

// RESULTATE
/*
$crawler = $client->request('GET', 'https://www.fvbj-afbj.ch/fussballverband-bern-jura/verband-fvbj/vereine-fvbj/verein-fvbj.aspx/v-1343/t-36636/ls-19012/sg-55270/a-pt/');
$crawler->filter('.spiel')->each(function ($node) {
    $status = $node->filter('.telegramm-link')->text();
    if (empty($status)){
        $spielnummer = $node->filter('.font-small')->text();
        if (endsWith($spielnummer, games)) {
            print $node->filter('.teamA')->text() . " vs " . $node->filter('.teamB')->text() . "\n";

            $node->filter('.goals .torA')->each(function ($goal) {
                print $goal->text() . " : ";
            });
            $node->filter('.goals .torB')->each(function ($goal) {
                print $goal->text() . "\n";
            });
        }
    }
});
*/

// VEREINE
ini_set('default_charset', 'utf-8');

$aux = new AuxParser(__DIR__);
$aux->associations();




