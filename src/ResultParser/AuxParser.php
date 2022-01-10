<?php
namespace ResultParser;


use Dotenv\Dotenv;
use Goutte\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PDO;
use Psr\Log\LoggerInterface;

class AuxParser {

    private $logger;
    private $db;
    private $client;

    public function __construct($dir = __DIR__){
        $dotenv = Dotenv::createImmutable($dir);
        $dotenv->load();

        $this->logger = $this->logger("AuxParser", $dir);
        $this->db = new PDO('mysql:host='.getenv('MYSQL_HOST').';dbname='.getenv('MYSQL_DATABASE'), getenv('MYSQL_USER'), getenv('MYSQL_PASS'));

        $this->client = new Client();
    }

    private function logger($name, $dir) : LoggerInterface {
        $loggerSettings = array(
            "path" => $dir . '/logs/app.log',
            "level" => Logger::DEBUG);
        $logger = new Logger($name);

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);

        return $logger;
    }

    public function associations(){
        $crawler = $this->client->request('GET', 'https://org.football.ch/ueber-uns/klubs/vereine-sfv.aspx');
        $crawler->filter('#ctl01_ctl10_NISVereinsliste_pnlVereine > .panel-primary')->each(function ($node) {
            $section = $node->filter('.panel-heading')->text();

            $current_verband="";
            if( preg_match( '!\(([^\)]+)\)!', $section, $match ) ) {
                $current_verband = $match[1];
                $name = self::trim($section, $match);
                $this->storeAssociation($name, $current_verband);
            }

            // VEREINE
            $node->filter('.panel-body li a')->each(function ($node) use ($current_verband) {
                $club = $node->text();
                if( preg_match( '!\(([^\)]+)\)!', $club, $match ) ){
                    $vereinsnummer = $match[1];
                    if (!is_numeric($vereinsnummer)){
                        $vereinsnummer = self::getVereinsnummer($club);
                    }
                    $vereinsname = self::trim($club, $match);
                    $this->storeClub($vereinsnummer, $vereinsname, $current_verband, $node->link()->getUri());
                }
            });
        });
    }

    private function storeAssociation(string $verbandsname, string $verband){
        $sql = "INSERT INTO verband_sfv ".
                    "(verbandsname, verband) VALUES ".
                    "(:verbandsname, :verband)";
        $values = array(
            ":verbandsname" => $verbandsname,
            ":verband" => $verband);

        $statement = $this->db->prepare($sql);
        $statement->execute($values);
    }

    private function storeClub(string $vereinsnummer, string $vereinsname, string $verband, string $url){
        $sql = "INSERT INTO verein_sfv ".
            "(vereinsnummer, vereinsname, vereinsid, verband, url) VALUES ".
            "(:vereinsnummer, :vereinsname, :vereinsid, :verband, :url)";
        $values = array(
            ":vereinsnummer" => $vereinsnummer,
            ":vereinsname" => $vereinsname,
            ":vereinsid" => self::getVereinsId($url),
            ":verband" => $verband,
            ":url" => $url);

        $statement = $this->db->prepare($sql);
        $statement->execute($values);
    }

    private static function trim($string, $match){
        $string = str_replace($match[0], "", $string);
        return trim($string);
    }

    private static function getVereinsnummer(string $name) : string {
        $pos1 = strrpos($name, "(");
        $text = substr($name, $pos1);
        return self::brackets($text);
    }

    private static function getVereinsId(string $url) : int {
        $pos1 = strrpos($url, "/v-");
        $id = substr($url, $pos1+3);
        return str_replace("/", "", $id);
    }

    private static function brackets(string $text) : string {
        preg_match('#\((.*?)\)#', $text, $match);
        return $match[1];
    }
}