<?php

session_set_cookie_params([
    'httponly' => true,
    'secure' => false,  

    'samesite' => 'Lax',
    'lifetime' => 86400  

]);

session_name('MARIKINA_AUTH');

?>
