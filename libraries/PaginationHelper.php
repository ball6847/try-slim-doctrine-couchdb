<?php
namespace Library;

class PaginationHelper
{
    public static function pageOffset($page, $limit = 20)
    {
        return ($page - 1) * $limit;
    }
}
