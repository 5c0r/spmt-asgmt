<?php
namespace App\Model;

interface SimpleHttpClientInterface {
    function getRequest(string $url, ?array $getData);
    function postRequest(string $url, array $postData);
}
