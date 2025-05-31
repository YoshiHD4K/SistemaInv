<?php
session_start();
session_unset();
session_destroy();
echo "<script>
    alert('Sesión cerrada correctamente.');
    window.location.href = 'index.php';
</script>";
exit();
?>