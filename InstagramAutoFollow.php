<?php

final class InstagramAutoFollow {

    protected $options;

    const sessionId = '';
    const INSTAGRAM_URL = 'https://www.instagram.com/';

    public function __construct() {
        $this->options = [
            'headers' => [
                'Referer' => self::INSTAGRAM_URL,
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => 'sessionid=' . self::sessionId . ';',
                'x-csrftoken' => 'OEfA8l9SdA34ZaMz6xOo17RuYnxSUKwy',
                'x-ig-app-id' => '936619743392459',
                'x-instagram-ajax' => '34ad0264803e',
                'x-requested-with' => 'XMLHttpRequest',
            ]
        ];
    }

    /**
     * Return either is followed or not
     * @param type $user
     * @return boolean
     */
    public function doFollow($user) {
        $info = $this->getInfo($user);
        if (!$info) {
            return false;
        }
        $userId = $info['graphql']['user']['id'];

        if ($info['graphql']['user']['followed_by_viewer']) {
            return true; // is already following this user
        }
        $followResponse = $this->follow($userId);

        if ($followResponse && $followResponse['status'] == 'ok') {
            return true;
        }
        return false;
    }

    /**
     * Get informations about user
     * @param type $user
     * @return boolean
     */
    private function getInfo($user) {
        $link = self::INSTAGRAM_URL . $user;
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $options = $this->options;
        $options['query'] = ['__a' => '1'];
        try {

            $response = $client->request('get', $link, $options);
        } catch (\Exception $ex) {
            return false;
        }

        $body = json_decode((string) $response->getBody(), true);

        return $body;
    }

    /**
     * Follow request accountId
     * @param type $accountId
     * @return type
     * @throws \Exception
     */
    private function follow($accountId) {
        $client = new \GuzzleHttp\Client(['http_errors' => false]);
        $response = $client->request('POST', sprintf('https://www.instagram.com/web/friendships/%s/follow/', $accountId), $this->options);
        if ($response->getStatusCode() == 403) {
            throw new \Exception('Please wait a few minutes before you try again');
        }
        $body = json_decode((string) $response->getBody(), true);

        return $body;
    }

}
