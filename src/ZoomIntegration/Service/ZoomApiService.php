<?php
declare(strict_types=1);

namespace App\ZoomIntegration\Service;

use App\ZoomIntegration\Entity\Zoom;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class ZoomApiService implements ZoomApiServiceInterface
{
    private Zoom $zoomEntity;

    public function __construct()
    {
        $this->zoomEntity = new Zoom($_ENV['ZOOM_API_KEY'], $_ENV['ZOOM_API_SECRET']);
    }

    public function createMeeting($data = [])
    {
        $requestUrl = "https://api.zoom.us/v2/users/zoom@basis33.com/meetings";

        $postTime = $data['start_date'];
        $startTime = gmdate("Y-m-d\TH:i:s", strtotime($postTime));

        $createMeetingArray = [];
        if (!empty($data['alternative_host_ids'])) {
            if (count($data['alternative_host_ids']) > 1) {
                $alternative_host_ids = implode(",", $data['alternative_host_ids']);
            } else {
                $alternative_host_ids = $data['alternative_host_ids'][0];
            }
        }


        $createMeetingArray['topic'] = $data['topic'];
        $createMeetingArray['agenda'] = !empty($data['agenda']) ? $data['agenda'] : "";
        $createMeetingArray['type'] = !empty($data['type']) ? $data['type'] : 2;
        $createMeetingArray['start_time'] = $startTime;
        $createMeetingArray['timezone'] = 'Asia/Tashkent';
        $createMeetingArray['password'] = !empty($data['password']) ? $data['password'] : "";
        $createMeetingArray['duration'] = !empty($data['duration']) ? $data['duration'] : 60;

        $createMeetingArray['settings'] = [
            'join_before_host' => !empty($data['join_before_host']),
            'host_video' => !empty($data['option_host_video']),
            'participant_video' => !empty($data['option_participants_video']),
            'mute_upon_entry' => !empty($data['option_mute_participants']),
            'enforce_login' => !empty($data['option_enforce_login']),
            'auto_recording' => !empty($data['option_auto_recording']) ? $data['option_auto_recording'] : "none",
            'alternative_hosts' => $alternative_host_ids ?? ""
        ];

        return $this->sendRequest($createMeetingArray, $requestUrl);
    }

    public function generateJWTKey(): string
    {
        $payload = array(
            "iss" => $this->zoomEntity->getApiKey(),
            "exp" => time() + 3600 //60 seconds as suggested
        );
        return JWT::encode($payload, $this->zoomEntity->getApiSecret(), 'HS256');
    }

    public function sendRequest(array $data, string $url)
    {

        $client = new Client(['base_uri' => $url]);

        $headers = [
            "authorization: Bearer " . $this->generateJWTKey(),
            "content-type: application/json",
            "Accept: application/json",
        ];

        $postFields = json_encode($data);

        $response = $client->request('POST', $url, [
            'curl' => [
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => $headers,
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }
}