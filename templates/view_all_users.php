<?php
declare(strict_types=1);
namespace App\View;

use App\Utils;

//var_dump($user);
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>View User</title>
</head>

<body style="font-size: 20px; color: #7f97c7; ">
    <h1>Список пользователей:</h1>
    <div>
        <?php
        foreach ($userList as $user) {
            ?>
            <div class="">
                <a style="text-decoration:none; font-size: 20px" class="user" href="/view_user/<?= htmlentities($user->getId()) ?>">
                    <label><?= $user->getFirstName() . ' ' . $user->getLastName() . ' ' . $user->getMiddleName() ?></label>
                </a>
                <a href="/delete_user/<?= htmlentities($user->getId()) ?>">
                    <button style="background-color:#c5d0e6; margin-left: 20px">Удалить</button>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</body>

</html>