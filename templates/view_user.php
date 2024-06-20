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
    <h1>Данные о пользователе:</h1>
    <label>First Name: </label><?= htmlentities((string) $user->getFirstName())?><br />
    <label>Last Name: </label><?= htmlentities((string) $user->getLastName())?><br />
    <label>Middle Name: </label><?= htmlentities((string) $user->getMiddleName())?><br />
    <label>Gender: </label><?= htmlentities((string) $user->getGender())?><br />
    <label>BirtDate: </label><?= htmlentities(Utils::convertDateTimeToStringForm($user->getBirthDate()))?><br />
    <label>Email: </label><?= htmlentities((string) $user->getEmail())?><br />
    <label>Phone: </label><?= htmlentities((string) $user->getPhone())?><br />
    <label>Avatar path:
        <?php
        if ($avatarPath != null):
            ?>
        </label><img width=200px src="<?= '/../../uploads/' . htmlentities($user->getAvatarPath())?>">
        <?php
        endif;
        ?>
    <br><br>
    <a style="text-decoration:none;" href="/update_user/<?= htmlentities($user->getId())?>">
        <button style="background-color:#c5d0e6;">Изменить</button>
    </a>

    <a style="text-decoration:none;" href="/delete_user/<?= htmlentities($user->getId())?>">
        <button style="background-color:#c5d0e6;">Удалить</button>
    </a>
</body>

</html>