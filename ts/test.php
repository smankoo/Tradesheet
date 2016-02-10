<?php
echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
date_default_timezone_set('America/Montreal');
echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
echo date("Y-m-d h:i:sa");
?>