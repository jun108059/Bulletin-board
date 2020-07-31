<?php

namespace App\Controllers;

use App\Models\Login;
use App\Models\Membership;
use App\Service\MailerService;
use App\Service\SessionManager;
use \Core\View;
use DateTime;


class MembershipController extends \Core\Controller
{
    /**
     * 1번 : 이메일 인증 Page
     * Render - View
     */
    public function signUpEmailAction()
    {
        // View 페이지 렌더링 해주기
        View::render('Membership/1.signUpEmail.php',);
    }

    /**
     * 2번 : 이메일 인증 번호 전송
     * Check - 메일 중복 & 인증 번호 전송
     * Render - VIew
     * @throws \Exception
     */
    public function sendMailAction()
    {
        $resultArray = ['result' => 'fail', 'alert' => ''];

        if (empty($_POST['email']) || empty($_POST['emAddress'])) {
            $resultArray['alert'] = '🧨이메일을 입력해주세요';
            echo json_encode($resultArray);
            exit();
        }

        $userMail = $_POST['email'] . '@' . $_POST['emAddress'];

        if (Membership::isEmailExisted($userMail)) {
            $resultArray['alert'] = '🟡 이미 사용 중인 Email 입니다. 🟡';
            echo json_encode($resultArray);
            exit();
        }

        $certify = random_int(100000, 999999); // 인증 번호 random 생성

//        $mailReturn = MailerService::mail($userMail, $certify);
        $mailReturn = true;
//        echo("Mailer 함수 주석처리");
//        echo("인증번호 = ".$certify);

        if ($mailReturn) {
            $resultArray['result'] = 'success';
            $resultArray['cert_num'] = $certify;
        }

        echo json_encode($resultArray);
        exit;
    }

    /**
     * 3번 : 회원 가입 Page Render
     * Render - View Sign Up
     */
    public function signUpAction()
    {
        if (empty($_POST['email']) || empty($_POST['emAddress'])) {
            echo '<script> alert("🧨이메일 정보를 입력해주세요"); history.back(); </script>';
            exit();
        }
        $userMail = $_POST['email'] . "@" . $_POST['emadress'];

        // View 페이지 렌더링 해주기
        View::render('Membership/2.signUp.php', [
            'mail' => $userMail
        ]);
    }

    /** 4번 : 가입 완료 버튼 -> DB data 넣기 */
    public function signUpDBAction()
    {
        // 비밀 번호 유효성 검사 - Script 에서 튕기는 코드 작성 후 삭제
        MembershipController::passwordCheck($_POST['password']);

        // 필수 값 검사
        if (empty($_POST['userId']) || empty($_POST['email']) || empty($_POST['password'])
            || empty($_POST['name']) || empty($_POST['phone'])) {
            return false;
        }

        $now = (new DateTime())->format('Y-m-d H:i:s');
        $userData = [
            'mem_user_id' => $_POST['userId'],
            'mem_email' => $_POST['email'],
            'mem_password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'mem_status' => 'Y', // enum 타입 - 정상 가입
            'mem_cert' => 'Y', // enum 타입 - 본인 인증 여부
            'mem_name' => $_POST['name'],
            'mem_phone' => $_POST['phone'],
            'mem_gender' => (!empty($_POST['gender'])) ?: '', // enum 타입
            'mem_level' => 4, // 일반 사용자 level 4
            'mem_reg_dt' => $now, // 회원 가입 일시
            'mem_log_dt' => $now, // 마지막 로그인 일시
            'mem_pw_dt' => $now, // 마지막 비밀 번호 변경 일시
        ];

        /**
         * 데이터 Insert
         * 서비스 할 때 사용자 한테 회원가입 page로 다시 안내
         * 관리자는 로그 파일로 에러 처리할 수 있도록 if else 설계
         */
        Membership::insertInfo($userData);

        // SignUp 완료 -> rendering
        View::render('Membership/3.signUpOK.php', [
            'id' => $userData['mem_user_id'],
            'name' => $userData['mem_name'],
            'email' => $userData['mem_email']
        ]);
        return true;
    }

    /**
     * ID 중복 검사
     */
    public function checkIdAction()
    {
        if (!empty($_POST['userId'])) {
            if (Membership::isUserExisted($_POST['userId'])) {
//            echo '<script> alert("🔴 이미 사용 중인 ID입니다. 🔴"); history.back(); </script>';
                echo "<span class='status-not-available'> 🔴이미 사용 중인 ID입니다.🔴</span>";
//            exit();
            } else {
//                echo "사용 가능한 ID 입니다";
                echo "<span class='status-available'> 🟢사용 가능한 ID 입니다.🟢</span>";
            }
        }
    }

    /**
     * 이메일 중복 검사
     */
    public function checkEmailAction()
    {
        if (!empty($_POST['email'])) {
            if (Membership::isEmailExisted($_POST['email'])) {
//            echo '<script> alert("🔴 이미 사용 중인 ID입니다. 🔴"); history.back(); </script>';
                echo "<span class='status-not-available'> 🔴이미 가입된 이메일입니다.🔴</span>";
//            exit();
            } else {
//                echo "사용 가능한 ID 입니다";
                echo "<span class='status-available'> 🟢사용 가능한 이메일입니다.🟢</span>";
            }
        }
    }

    /**
     * 핸드폰 번호 중복 검사
     */
    public function checkPhoneAction()
    {
        if (!empty($_POST['phone'])) {
            if (Membership::isPhoneExisted($_POST['phone'])) {
                echo "<span class='status-not-available'> 🔴이미 가입된 번호입니다.🔴</span>";
            } else {
                echo "<span class='status-available'> 🟢사용 가능한 번호입니다.🟢</span>";
            }
        }
    }

    /**
     * ID 찾기
     * render - ID Show
     */
    public function findIdAction()
    {
        // front 에서 검사 했지만 바로 접근하는 경우 때문에 한번 더 검사
        if (empty($_POST['email']) || empty($_POST['emAddress'])) {
            echo '<script> alert("🧨이메일 정보를 입력해주세요"); history.back(); </script>';
            exit();
        }

        // 이메일이 DB에 존재하는지
        $userMail = $_POST['email'] . "@" . $_POST['emadress'];
        if (Membership::isEmailExisted($userMail)) {
            // 입력 받은 mail과 일치하는 ID 찾기
            $userId = Membership::findId($userMail);
            // View 페이지 렌더링 해주기
            View::render('Membership/findAndShowID.php', [
                'userId' => $userId
            ]);
        } else {
            // 존재하지 않는 Email인 경우 튕겨주기
            echo '<script> alert("🧨가입된 이메일이 아닙니다."); history.back(); </script>';
        }
    }

    /**
     * PW 찾기
     * render - PW 재 설정
     */
    public function findPwAction()
    {
        // front 에서 검사 했지만 바로 접근하는 경우 때문에 한번 더 검사
        if (empty($_POST['user_id'])) {
            echo '<script> alert("🧨올바른 접근이 아닙니다."); history.back(); </script>';
            exit();
        } else {
            if (Membership::isUserExisted($_POST['user_id'])) {
                // 올바른 User ID를 입력한 경우
                // 비밀 번호 찾을 User 조회
                $user = Login::getUserData($_POST['user_id']);
                // 본인 인증 메일
                $userEmail = $user['mem_email'];

                // 본인 인증할 이메일 전송 하기
                // 고객님이 가입하셨던 이메일은 ***이렇습니다.
                // 본인 인증 메일을 전송할까요?
                //
                // View 페이지 렌더링 해주기
                View::render('Membership/findAndResetPw.php', [
                    'userEmail' => $userEmail,
                    'userID' => $_POST['user_id']
                ]);

            } else {
                echo '<script> alert("🧨존재하지 않는 아이디입니다."); history.back(); </script>';;
            }
        }
    }

    /**
     * 개인 정보 수정 Page Render
     * Render - View findMyInfo
     */
    public function findMyInfoAction()
    {
        View::render('Membership/findMyInfo.php', []);
    }

    /**
     * Before filter
     *
     */
    protected function before()
    {
//        $session_manager = new SessionManager();
//        // 로그인 되어 있으면 튕기기
//        if ($session_manager->isValidAccess()) {
//            echo '<script> alert("잘못된 접근입니다."); history.back(); </script>';
//            return false;
//        } else {
//            return true;
//        }

    }

    /**
     * (삭제 예정) 비밀 번호 유효성 검사 함수
     * @param $_password
     */
    protected function passwordCheck($_password)
    {
        $pw = $_password;
        $num = preg_match('/[0-9]/u', $pw);
        $eng = preg_match('/[a-z]/u', $pw);
        $spe = preg_match("/[\!\@\#\$\%\^\&\*]/u", $pw);

        if (strlen($pw) < 8 || strlen($pw) > 21) {
            echo '<script> alert("🔴 비밀번호는 8자리 ~ 20자리 이내로 입력해주세요. 🔴"); history.back(); </script>';
            exit();
        }

        if (preg_match("/\s/u", $pw) == true) {
            echo '<script> alert("🟡 비밀번호는 공백없이 입력해주세요. 🟡"); history.back(); </script>';
            exit();
        }
        // 테스트 때문에 삭제 - 특수 문자 그냥 뺄지 고민 중
//        if ($num == 0 || $eng == 0 || $spe == 0) {
//            echo '<script> alert("🟠 비밀번호는 영문, 숫자, 특수문자를 혼합하여 입력해주세요. 🟠"); history.back(); </script>';
//            exit();
//        }
    }

    public function checkPwAction()
    {
        if (!empty($_POST['userId']) && !empty($_POST['currPw'])) {
            if (Membership::checkPassword($_POST['userId'], $_POST['currPw'])) {
//            echo '<script> alert("🔴 이미 사용 중인 ID입니다. 🔴"); history.back(); </script>';
                echo "<span class='status-not-available'> 🟢일치합니다.🟢</span>";
//            exit();
            } else {
//                echo "사용 가능한 ID 입니다";
                echo "<span class='status-available'> 🔴비밀번호가 일치하지 않습니다.🔴</span>";
            }
        }
    }

    public function sendMail2Action()
    {
        $resultArray = ['result' => 'fail', 'alert' => ''];

        if (empty($_POST['email'])) {
            $resultArray['alert'] = '🧨이메일을 입력해주세요';
            echo json_encode($resultArray);
            exit();
        }

        $userMail = $_POST['email'];


        $certify = random_int(100000, 999999); // 인증 번호 random 생성

//        $mailReturn = MailerService::mail($userMail, $certify);
        $mailReturn = true;
//        echo("Mailer 함수 주석처리");
//        echo("인증번호 = ".$certify);

        if ($mailReturn) {
            $resultArray['result'] = 'success';
            $resultArray['cert_num'] = $certify;
        }

        echo json_encode($resultArray);
        exit;
    }
}