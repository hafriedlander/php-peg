<?php
ini_set("pcre.jit", "0");

require 'autoloader.php';

use hafriedlander\Peg\Compiler;

Compiler::cli( $_SERVER['argv'] ) ;
