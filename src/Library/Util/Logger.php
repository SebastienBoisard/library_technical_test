<?php

namespace Library\Util;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger as MonologLogger;

/**
 * Logger gère l'écriture des logs à travers la bibliothèque Monolog.
 * L'intérêt de cette classe est d'avoir une seule instance pour écrire les logs, qui soit facile d'utilisation,
 * avec une instanciation simple, et uniforme à travers tout le projet.
 *
 */
class Logger
{
    /** @var Logger - Instance de la classe utilisée à travers le pattern design dit "singleton" */
    private static $_oInstance = null;

    /**
     * @var \Monolog\Logger - Instance Monolog gérant le stockage des logs sur disque
     */
    private $_oLogger;

    /** @var bool - Désactive les logs (utile pour les tests unitaires) */
    private $disabled = false;


    /**
     * Le constructeur dans un pattern "singleton" est toujours privé afin d'éviter une instanciation classique
     * de la classe.
     */
    private function __construct()
    {
    }

    /**
     * getInstance est le seule moyen de récupérer l'instance de la classe Logger.
     *
     * @return Logger
     */
    public static function getInstance()
    {
        if (self::$_oInstance == null) {
            self::$_oInstance = new Logger();
            self::$_oInstance->_createLogger();
        }

        return self::$_oInstance;
    }

    /**
     * disable désactive la gestion des logs. 
     * Cette fonction est surtout utile pour éviter que les logs viennent gêner les tests unitaires.
     */
    public function disable()
    {
        $this->disabled = true;
    }

    /**
     * _createLogger créé le gestionnaire de logs grâce à différentes classes de Monolog.
     */
    private function _createLogger()
    {
        // Création du stream gérant les logs de tous les niveaux avec une gestion rotative des logs
        // Ainsi, tous les jours, un nouveau fichier de logs sera créé (exemple : debug-2017-11-21.log).
        $oStream = new RotatingFileHandler(
            __DIR__.'/../../../logs/bm.log',
            0,
            MonologLogger::DEBUG
        );

        // Création d'un formatage spécifique pour les logs
        // Le format par défaut de la date est "Y-m-d H:i:s"
        $sDateFormat = "Y.m.d H:i:s";
        // Le format par défaut de l'affichage est "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        $sOutput = "[%datetime%] %level_name% %message%\n";
        // Création du formatage
        $oFormatter = new LineFormatter($sOutput, $sDateFormat, true);
        // Ajout du formatage au stream de debug
        $oStream->setFormatter($oFormatter);

        // Création du logger à partir de la bibliothèque Monolog.
        $this->_oLogger = new MonologLogger('WebAss');
        $this->_oLogger->pushHandler($oStream);
    }

    /**
     * _createHeader rassemble les infos à mettre au début de chaque ligne des logs :
     *    . l'adresse ip de l'utilisateur,
     *    . le pid du process
     *    . le nom de la classe appelante
     *    . le nom de la fonction appelante
     *    . le numéro de la ligne appelante
     *
     * L'entête retournée a le format suivant :
     * adresse_ip numero_processus_PHP nom_de_la_classe::nom_de_la_fonction[numero_ligne] -
     *
     * Exemple :
     * 127.0.0.1 3390 Controller\AuthController::indexAction[83] -
     *
     * @param \Exception $oException - instance de l'exception si le debug est associé à une exception, sinon null
     * @return string - entête d'une ligne dans les logs
     */
    private function _createHeader(\Exception $oException = null)
    {
        if ($oException != null) {
            $aBacktrace = $oException->getTrace();
            $sLocation = $aBacktrace[0]['class'].'::'.$aBacktrace[0]['function'];
        } else {

            // Construction du lieu d'appel de cette fonction de débugage.
            // Pour pouvoir retrouver depuis quelle classe/function cette fonction de debug a été appelée,
            // il faut utiliser la stack d'appel.
            $aBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

            // Vu que la backtrace est appelée depuis cette fonction, il faut aller récupérer la seconde ligne pour
            // avoir le nom de la véritable fonction qui a appelé la fonction de log.
            $iLevel = 2;

            if (isset($aBacktrace[$iLevel]) == true) {
                $sLocation = $aBacktrace[$iLevel]['class'].'::'.$aBacktrace[$iLevel]['function'];
                if (isset($aBacktrace[$iLevel-1]['line']) == true) {
                    $sLocation = $sLocation.'['.$aBacktrace[$iLevel-1]['line'].']';
                }
            } else {
                $sLocation = 'unknown';
            }
        }

        return $_SERVER['REMOTE_ADDR'].' '.getmypid().' '.$sLocation.' - ';
    }

    /**
     * addDebug ajoute une ligne dans les logs avec le niveau DEBUG.
     *
     * Les logs sont au format :
     * [Y.m.d H:i:s] level adresse_ip numero_processus_PHP nom_de_la_classe::nom_de_la_fonction[numero_ligne] - message
     *
     * Exemple :
     * [2017.11.21 15:37:38] DEBUG 127.0.0.1 3390 Controller\AuthController::indexAction[83] - message
     *
     * @param $sMessage - texte à écrire dans les logs
     */
    public function addDebug($sMessage)
    {
        if ($this->disabled == true) {
            return;
        }

        $this->_oLogger->debug($this->_createHeader().$sMessage);
    }

    /**
     * addInfo ajoute une ligne dans les logs avec le niveau INFO.
     *
     * Les logs sont au format :
     * [Y.m.d H:i:s] level adresse_ip numero_processus_PHP nom_de_la_classe::nom_de_la_fonction[numero_ligne] - message
     *
     * Exemple :
     * [2017.11.21 15:37:38] INFO 127.0.0.1 3390 Controller\AuthController::indexAction[83] - message
     *
     * @param string $sMessage - texte à écrire dans les logs
     */
    public function addInfo($sMessage)
    {
        if ($this->disabled == true) {
            return;
        }

        $this->_oLogger->info($this->_createHeader().$sMessage);
    }

    /**
     * addWarning ajoute une ligne dans les logs avec le niveau WARNING.
     *
     * Les logs sont au format :
     * [Y.m.d H:i:s] level adresse_ip numero_processus_PHP nom_de_la_classe::nom_de_la_fonction[numero_ligne] - message
     *
     * Exemple :
     * [2017.11.21 15:37:38] WARNING 127.0.0.1 3390 Controller\AuthController::indexAction[83] - message
     *
     * @param string $sMessage - texte à écrire dans les logs
     */
    public function addWarning($sMessage)
    {
        if ($this->disabled == true) {
            return;
        }

        $this->_oLogger->warning($this->_createHeader().$sMessage);
    }

    /**
     * addError ajoute une ligne dans les logs avec le niveau ERROR.
     *
     * Les logs sont au format :
     * [Y.m.d H:i:s] level adresse_ip numero_processus_PHP nom_de_la_classe::nom_de_la_fonction[numero_ligne] - message
     *
     * Exemple :
     * [2017.11.21 15:37:38] ERROR 127.0.0.1 3390 Controller\AuthController::indexAction[83] - message
     *
     * @param string $sMessage - texte à écrire dans les logs
     */
    public function addError($sMessage)
    {
        if ($this->disabled == true) {
            return;
        }

        // écriture du header
        $sStartMessage = "\n<--START------------------------------------------------------------\n";

        $sExtendedMessage = "Message:\n    " . $sMessage .
            "\n-------------------------------------------------------------------\n";

        $sExtendedMessage .= $this->_writeContext();

        $sBacktrace = "Trace:\n" .
            var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 0), true) . "\n";

        $sEndMessage = "--END-------------------------------------------------------------->";


        $this->_oLogger->error($this->_createHeader().$sStartMessage.$sExtendedMessage.
            $sBacktrace.$sEndMessage);
    }

    /**
     * addCritical ajoute une ligne dans les logs avec le niveau CRITICAL uniquement pour la gestion des exceptions.
     * Ces logs sont également envoyés par Slack et par Mail.
     *
     * Les logs sont au format :
     * [Y.m.d H:i:s] level adresse_ip numero_processus_PHP nom_de_la_classe::nom_de_la_fonction[numero_ligne] - message
     *
     * Exemple :
     * [2017.11.21 15:37:38] ERROR 127.0.0.1 3390 Controller\AuthController::indexAction[83] - message
     *
     * @param \Exception $oException - exception dont il faut détailler le contenu dans les logs
     * @param string message - message à rajouter dans les logs (vide par défaut)
     */
    public function addCritical(\Exception $oException, $sMessage = "")
    {
        if ($this->disabled == true) {
            return;
        }

        $sTrace = $oException->getTraceAsString();

        $oTmpException = $oException;

        $i = 1;
        do {
            $aMessages[] = "    ". $i++ . ": " . $oTmpException->getMessage();
        } while ($oTmpException = $oTmpException->getPrevious());

        // écriture du header
        $sStartMessage = "\n<--START------------------------------------------------------------\n";

        $sExtendedMessage = '';
        if ($sMessage != '') {
            $sExtendedMessage .= "Message:\n    " . $sMessage .
                "\n-------------------------------------------------------------------\n";
        }

        $sExtendedMessage .= "Exception:\n" . implode("\n", $aMessages) .
            "\n-------------------------------------------------------------------\n";

        $sExtendedMessage .= $this->_writeContext();

        $sBacktrace = "Trace:\n" . $sTrace . "\n";

        $sEndMessage = "--END-------------------------------------------------------------->";

        // écriture dans le logger
        $this->_oLogger->critical($this->_createHeader($oException).$sStartMessage.$sExtendedMessage.
            $sBacktrace.$sEndMessage);
    }

    /**
     * _writeContext retourne le contexte (adresse IP, url, etc.) associé à un utilisateur.
     *
     * @return string
     */
    private function _writeContext()
    {
        return "Context:\n" .
            '    user address : ' . $_SERVER['REMOTE_ADDR'] . "\n" .
            '    uri : ' . $_SERVER['REQUEST_URI'] . "\n" .
            '    referer : ' . (isset($_SERVER['HTTP_REFERER']) == true ? $_SERVER['HTTP_REFERER'] : 'none')."\n" .
            "-------------------------------------------------------------------\n";
    }
}