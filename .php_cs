<?php

$finder = Symfony\CS\Finder::create()->in([__DIR__]);
return Symfony\CS\Config::create()->finder($finder);
