<?php
namespace App\model;

interface DataProcessingInterface
{
    function getAverageCharacterLengthPerMonth();
    function getAveragePostPerUserPerMonth();
    function getLongestPostPerMonth();
    function getTotalPostsSplitByWeek();

    function initializeDatabase();
    function initializeData();
}