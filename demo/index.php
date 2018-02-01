<?php

require '../DependencyInjectionBuilder.php';

$di = new DependencyInjectionBuilder();
$b = $di->create('B');
var_dump($b);

class A
{
}

class B
{
    public function __construct(A $a)
    {
    }
}
