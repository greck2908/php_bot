<?php


class Telegram {
    const URL_BASE = 'https://api.telegram.org/bot';

    private $token;
    private $url;

    public function __construct($token) {
        $this->token = $token;
        $this->url = self::URL_BASE . $token . '/';
    }

    public function getUpdates($offset) {
        return $this->request('getUpdates', array('offset' => $offset));
    }

    public function sendMessage($chatId, $text) {
        return $this->request('sendMessage', array('chat_id' => $chatId, 'text' => $text));
    }

    /**
     * Метод отправки файлов/документов
     * @param  [int]  $chatId     [ID чата]
     * @param  [string] $pathToFile [ПОЛНЫЙ ПУТЬ к файлу от корня]
     * @return [type]             [description]
     */
    public function sendDocument($chatId, $pathToFile){
        return $this->request('sendDocument', ["chat_id" => $chatId, "document" => new CURLFile(realpath($pathToFile))]);
    }

    /**
     * Метод отправки изображений/фотографий
     * @param  [int]  $chatId     [ID чата]
     * @param  [string] $pathToFile [ПОЛНЫЙ ПУТЬ к файлу картинки от корня]
     * @return [type]             [description]
     */
    public function sendPhoto($chatId, $pathToFile){
        return $this->request('sendPhoto', ["chat_id" => $chatId, "photo" => new CURLFile(realpath($pathToFile))]);
    }

    private function request($tgMethod, $params = array()) {
        $url = $this->url . $tgMethod;
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($response, true);

        if(!empty($data) && $data["ok"]){
            return $data['result'];
        }

        return $data;
    }
}