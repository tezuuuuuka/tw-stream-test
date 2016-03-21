<?php
for ( $i=1; $i<=100; $i++) {
    echo "[$i]<br>\n";
    flush();
    ob_flush();
    sleep(1);
}

