<?php

$con = mysqli_connect("dumont", "240224", "240224", "240224_web");

if (mysqli_connect_errno()) {
    echo "Falha ao se conectar ao MySQL: " . mysqli_connect_erro();
} else {
    mysqli_select_db($con, "banco");
}

?>