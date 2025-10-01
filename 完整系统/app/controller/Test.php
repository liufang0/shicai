<?php
declare(strict_types=1);

namespace app\controller;

class Test
{
    public function index()
    {
        return 'Hello Test Controller!';
    }
    
    public function admin()
    {
        return 'Admin Test!';
    }
}