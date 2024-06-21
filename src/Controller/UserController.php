<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private const DATE_TIME_FORMAT = 'Y-m-d';
    private const SUPPORT_MIME_TYPES = [
        'image/png' => 'png',
        'image/jpeg' => 'jpeg',
        'image/gif' => 'gif',
    ];

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(): Response
    {
        return $this->render('register_user_form.html.twig');
    }
    private function getAvatarExtension(string $mimeType): ?string
    {
        return self::SUPPORT_MIME_TYPES[$mimeType] ?? null;
    }

    public function registerUser(Request $data): ?Response
    {
        $birthDate = Utils::parseDateTime($_POST['birth_date'], self::DATE_TIME_FORMAT);
        $birthDate = $birthDate->setTime(0, 0, 0);

        $user = new User(
            null,
            $data->get('first_name'),
            $data->get('last_name'),
            empty($data->get('middle_name')) ? null : $data->get('middle_name'),
            $data->get('gender'),
            $birthDate,
            $data->get('email'),
            empty($data->get('phone')) ? null : $data->get('phone'),
            null,
        );

        if ($this->userRepository->findByEmail($data->get('email')) != null) {
            $mess = 'A user with such an email already exists!';
            return $this->redirectToRoute('pageWithError', ['mess' => $mess]);
        }

        $userId = $this->userRepository->store($user);
        $file = $this->downloadImage($userId);

        if ($file != null) {
            $user->setAvatarPath($file);
            $this->userRepository->store($user);
        }

        return $this->redirectToRoute('view_user', ['userId' => $userId], Response::HTTP_SEE_OTHER);
    }

    private function downloadImage(int $id): ?string
    {
        $uploadfile = __DIR__ . '/../../public/uploads/avatar';
        $file = null;

        if ($_FILES['avatar_path']['error'] == 0) {
            $extension = $this->getAvatarExtension($_FILES['avatar_path']['type']);
            if ($extension == null) {
                $mess = 'Error with extension!';
                return $this->redirectToRoute('pageWithError', ['mess' => $mess]);
            }
            if (move_uploaded_file($_FILES['avatar_path']['tmp_name'], $uploadfile . $id . '.' . $extension)) {
                $file = 'avatar' . $id . '.' . $extension;
            }
        }
        return $file;
    }

    public function updateUser(int $userId, Request $data): Response
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            $mess = 'You can not update user with this ID';
            return $this->redirectToRoute('pageWithError', ['mess' => $mess]);
        }

        if ($data->isMethod('post')) {
            if ($this->userRepository->findByEmail($data->get('email')) != null) {
                $mess = 'The user with this email already exists';
                return $this->redirectToRoute('error_page', ['mess' => $mess]);  
            } 
            $user = $this->updateUsersData($data);
            echo('Данные успешно обновлены!');
        }

        return $this->render('update_user_form.html.twig', [
            'userId' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'middleName' => $user->getMiddleName(),
            'gender' => $user->getGender(),
            'birthDate' => Utils::convertDateTimeToStringForm($user->getBirthDate()),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'avatarPath' => $user->getAvatarPath(),
        ]);
    }
    private function updateUsersData(Request $data): User
    {
        $id = (int) $data->get('user_id');
        $user = $this->userRepository->findById($id);

        $birthDate = Utils::parseDateTime($data->get('birth_date'), self::DATE_TIME_FORMAT);
        $birthDate = $birthDate->setTime(0, 0, 0);

        if ($this->userRepository->findByEmail($data->get('email')) != null) {
            header('Location: ' . '/pageWithError.php', true, 303);
        }

        if ($user != null) {
            $user->setFirstName($data->get('first_name'));
            $user->setLastName($data->get('last_name'));
            $user->setMiddleName(empty($data->get('middle_name')) ? null : $data->get('middle_name'));
            $user->setGender($data->get('gender'));
            $user->setBirthDate($birthDate);
            $user->setEmail(empty($data->get('email')) ? null : $data->get('email'));
            $user->setPhone(empty($data->get('phone')) ? null : $data->get('phone'));
        } else {
            header('Location: ' . '/pageWithError.php', true, 303);
        }

        $file = $this->downloadImage($id);

        if ($file != null) {
            $user->setAvatarPath($file);
        }

        $this->userRepository->store($user);
        return $user;
    }

    private function deleteImage(User $user): void
    {
        $avatarPath = $user->getAvatarPath();
        $filePath = __DIR__ . '/../../public/uploads/' . $avatarPath;
        if (file_exists($filePath)) {
            unlink($filePath);
            echo "File Successfully Delete.";

        } else {
            echo "File does not exists";
        }
    }
    public function deleteUser(int $userId): Response
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            $mess = 'There is no such user!';
            return $this->redirectToRoute('pageWithError', ['mess' => $mess]);
        }
        $this->userRepository->delete($user);
        if ($user->getAvatarPath() != null) {
            $this->deleteImage($user);
        }
        return $this->redirectToRoute('view_all_users');
    }

    public function viewUser(int $userId): Response
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            $mess = 'There is not user with this ID';
            return $this->redirectToRoute('pageWithError', ['mess' => $mess]);
        }

        return $this->render('view_user.html.twig', [
            'userId' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'middleName' => $user->getMiddleName(),
            'gender' => $user->getGender(),
            'birthDate' => Utils::convertDateTimeToStringForm($user->getBirthDate()),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'avatarPath' => $user->getAvatarPath(),
        ]);
    }

    public function viewAllUsers(): Response
    {
        $view_all_users = $this->userRepository->listAll();
        return $this->render('view_all_users.html.twig', ['view_all_users' => $view_all_users]);
    }
    public function pageWithError(string $mess): Response
    {
        return $this->render('pageWithError.html.twig', ['mess' => $mess]);
    }
}