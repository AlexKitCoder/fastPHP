<?php
namespace Core\Driver;
use Core\Driver\Interfaces\LogInterface;
class Log implements LogInterface
{
    private static $instance;
    private static $logLevel;
    private function __construct()
    {
        self::$logLevel = strtolower(config('log_level'));
        self::createLogFile();
    }

    public static function getInstance()
    {
        if(!self::$instance) {
            return new self();
        }
        return self::$instance;
    }

    public function info(string $message, array $context = [])
    {
        if(self::$logLevel == 'info') self::writeLogIntoFile(self::createLogData($message, 'info'));
    }

    public function notice(string $message, array $context = [])
    {
        if(self::$logLevel == 'notice' || self::$logLevel == 'info') self::writeLogIntoFile(self::createLogData($message, 'notice'));
    }

    public function warning(string $message, array $context = [])
    {
        if(self::$logLevel == 'warning' || self::$logLevel == 'notice' || self::$logLevel == 'info') self::writeLogIntoFile(self::createLogData($message, 'warning'));
    }

    public function error(string $message, array $context = [])
    {
        self::writeLogIntoFile(self::createLogData($message, 'error'));
    }

    private static function logFileName()
    {
        return APP_ROOT.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.date("Ymd",time()).'.log';
    }

    private static function logFileExist(string $fileName = '')
    {
        $fileName = empty($fileName) ? date("Ymd",time()).'.log' : $fileName;
        if(file_exists(APP_ROOT.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.$fileName)) {
            return true;
        } else {
            return false;
        }
    }

    private static function createLogData(string $message, string $level)
    {
        $lineData = "\r\n[".date("Y-m-d H:i:s",time()).'] ['.$level.']'."\r\n";
        $lineData .= "[timeCost: ".round((microtime(true)-APP_STARTTIME),6).'s]'."\r\n";
        $lineData .= '[message]: '.$message."\r\n";
        return $lineData;
    }

    private static function writeLogIntoFile(string $message)
    {
        if(self::logFileExist()) {
            file_put_contents(self::logFileName(),$message, FILE_APPEND);
        } else {
            throw new \Exception('log file: '.self::logFileName().' not exist!');
        }
    }

    private static function createLogFile()
    {
        if(!self::logFileExist()) {
            file_put_contents(self::logFileName(), '', FILE_APPEND);
        } else {
            return true;
        }
    }

    private function __clone() {}
}