<?php
declare(strict_types=1);

namespace App\ZoomIntegration\Service;

interface ZoomApiServiceInterface
{
//    public function generateJWTKey(): string;

    public function sendRequest(array $data, string $url);

}