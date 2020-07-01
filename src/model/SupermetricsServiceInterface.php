<?php

namespace App\Model;

interface SupermetricsServiceInterface {
    function authenticate(): bool;
    function getPosts(int $page_number): array ;
    function getPostsFromMultiplePages(int $from_page, int $to_page): array;
}