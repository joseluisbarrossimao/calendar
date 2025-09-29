<?php

require_once __DIR__ . "/src/path.php";

if (isset($_POST['method'])) {
    if ($_POST['method'] == 'event') {
        require_once __DIR__ . "/example/event.php";
    } else {
        require_once __DIR__ . "/example/example.php";
    }
} else {
    require_once __DIR__ . "/example/example.php";
}