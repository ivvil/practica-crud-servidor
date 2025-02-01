<?php

$req = $_SERVER['REQUEST_URI'];

$login = 'style="display:none"';
$register = 'style="display:none"';

switch ($req) {
    case '/login':
        $login = '';
        break;
    case '/register':
        $register = '';
        break;
}

// if () {
    
// }

?>

<main role="tablist">
    <button hx-get="/ui/login" role="tab" aria-controls="tab-content">
        <h2><strong>01</strong> Login</h2>
        <hr>
    </button>
    <div id="tab-content" role="tabpanel" class="tab-content" <?= $login ?>>
        <form method="post" action="index.php">
            <label for="user">USR:</label>
            <input type="text" name="user">
            <br>
            <label for="password">PSSWD:</label>
            <input type="password" name="password">
            <br>
            <input type="submit" value="LOGIN">
        </form>
    </div>
    <br>
    <button hx-get="/register" role="tab" aria-controls="tab-content">
        <h2><strong>02</strong> Register</h2>
        <hr>
    </button>
    <div id="tab-content" role="tabpanel" class="tab-content" <?= $register ?>>
        <form method="post" action="index.php">
            <label for="user">USR:</label>
            <input type="text" name="user">
            <br>
            <label for="password">PSSWD:</label>
            <input type="password" name="password">
            <br>
            <label for="re-password">REPEAT PSSWD:</label>
            <input type="password" name="re-password">
            <br>
            <input type="submit" value="REGISTER">
        </form>
    </div>
</div>
