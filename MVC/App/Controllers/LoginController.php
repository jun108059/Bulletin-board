<?php

namespace App\Controllers;

use \Core\View;
use App\Models\Login;
use DateTime;

class LoginController extends \Core\Controller
{

    /**
     * 메인 페이지
     * @return void
     */
    public function indexAction()
    {
        if (!empty($_SESSION['userID'])) {
            if ($_SESSION['userID'] === 'admin' && $_SESSION['userLevel'] === '1') {
                View::render('Admin/loginOK.html', []);
            } else {
                View::render('Login/loginOK.html', []);
            }
        } else {
            View::render('Login/index.php', []);
        }
    }

    /**
     * Login 검사
     */
    public function loginCheckAction()
    {
        // 각 변수에 ID, PW 저장
        $user_id = $_POST['user_id'];
        $user_pw = $_POST['user_password'];

        // View & Back-end 이중 체크
        if (empty($user_id) || empty($user_pw)) { // empty 로 빈값 체크
            View::render('Error/errorPage.php', [
                'alert' => "아이디 또는 패스워드 입력하세요❗",
                'back' => "ture"
            ]);
            exit();
        }

        // 모델 에서 데이터 꺼내 오기
        $user = Login::getUserData($user_id);
        if (empty($user)) {
            View::render('Error/errorPage.php', [
                'alert' => "존재하지 않는 User 입니다❗",
                'back' => "ture"
            ]);
            exit();
        }
        $pw_check = $user['mem_password'];
//        $user_log = $user['mem_log_dt'];
        $status_check = $user['mem_status']; // 계정 상태 확인


        //만약 password 와 hash_pw 가 같다면 세션 실행
        if (password_verify($user_pw, $pw_check)) {
            if ($status_check === 'H') {
                echo '<script> alert("고객님은 휴면계정입니다!🔒"); </script>';
                $_SESSION["userID"] = $user_id;
                $_SESSION["userEmail"] = $user['mem_email'];
                View::render('Login/dormant.php', [
                    'user_email' => $_SESSION["userEmail"]
                ]);
                exit();
            }
            $user['mem_log_dt'] = (new DateTime())->format('Y-m-d H:i:s');
            $logValue = "IN";
            // 로그인 - 활동 시간 갱신
            if(Login::updateLogInDate($user)) {
                // 로그인 성공 시 Log Table 에 삽입
                if(!(Login::logTableInsert($user, $logValue))){
                    View::render('Error/errorPage.php', [
                        'alert' => "오류가 발생했습니다. 로그인을 다시 시도해주세요.",
                        'back' => "ture"
                    ]);
                    exit();
                }
            }

            $_SESSION["userID"] = $user_id;
            $_SESSION["userLog"] = $user['mem_log_dt'];
            $_SESSION["userLevel"] = $user['mem_level'];
            // todo session 로그추가
            if ($_SESSION["userLevel"] === '1') {
                View::render('Admin/loginOK.html', []);
            } else {
                View::render('Login/loginOK.html', []);
            }
        } else { // 비밀번호가 같지 않다면 알림창을 띄우고 전 페이지로 돌아갑니다
            echo "<script>alert('❗ 아이디 또는 비밀번호를 확인하세요 ❗'); history.back();</script>";
        }

    }

    /**
     * 로그아웃 -> session 제거 & View Render
     */
    public function logoutAction()
    {
        session_start();
        $userId = $_SESSION['userID'];
        $user = Login::getUserData($userId);
        $user['mem_log_dt'] = (new DateTime())->format('Y-m-d H:i:s');
        $logValue = "OUT";
        Login::logTableInsert($user, $logValue);
        session_destroy();
        View::render('Login/logout.html');
    }

}