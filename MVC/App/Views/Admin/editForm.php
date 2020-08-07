<?php
include('head.php');
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="Content-Type" content="text/html;
    charset=UTF-8" />
    <title>사용자 정보</title>
    <script type="text/javascript" src="https://code.jquery.com/jquery.min.js"></script>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <script src="/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div>
        <h1 class="h2" align="center">&nbsp; 사용자 정보 수정<a class="btn btn-success" href="/Admin/index" style="margin-left: 850px"><span class="glyphicon glyphicon-home"></span>&nbsp; Back</a></h1><hr>
    </div>
    <form id="myForm" class="form-horizontal" method="post" style="margin: 0 150px 0 150px;border: solid 1px;border-radius:4px">

        <input type="hidden" id="collect_password" value="n">
        <input type="hidden" id="collect_password_reg" value="n">
        <input type="hidden" id="collect_tell" value="n">
        <input type="hidden" id="collect_name" value="n">

        <table width="500" height="650" class="table table-responsive">
            <tr>
                <td><label class="control-label" style="color: dodgerblue;">아이디</label></td>
                <td>
                    <input type="text" id="userId" name="userId" class="form-control" value="<?php echo $user_id; ?>"
                           autocomplete="off" readonly />
                </td>
                <td>
                    <label class="control-label" style="color: dodgerblue;"> 수정 불가능</label>
                </td>
            </tr>
            <tr>
                <td><label class="control-label" style="color: dodgerblue;">이메일</label></td>
                <td>
                    <input type="text" id="userEmail" name="userEmail" class="form-control" value="<?php echo $user_email; ?>"
                           autocomplete="off" readonly />
                </td>
                <td>
                    <label class="control-label" style="color: dodgerblue;"> 수정 불가능</label>
                </td>
            </tr>
            <tr>
                <td><label class="control-label">비밀번호</label></td>
                <td><input type="password" name="password" placeholder="비밀번호(8 ~ 20 글자)" autocomplete="off"
                           class="form-control" id="pw1" maxlength="20"
                           readonly onfocus="this.removeAttribute('readonly');" required></td>
                <td>
                    <div id="check-pw-success" style="display: none; color: limegreen; font-weight: bold;">&nbsp;🟢사용가능한 비밀번호🟢</div>
                    <div id="check-pw-fail" style="display: inline; color: orange; font-weight: bold;">&nbsp;❌8~20자리로 영문, 숫자 포함❌</div>
                </td>
            </tr>
            <tr>
                <td><label class="control-label">비밀번호 확인</label></td>
                <td><input type="password" name="password2" placeholder="비밀번호 확인" autocomplete="off"
                           class="form-control" id="pw2" maxlength="20"
                           readonly onfocus="this.removeAttribute('readonly');" required></td>
                <td>
                    <div id="alert-success" style="display: none; color: blue; font-weight: bold;"> 😄비밀번호 일치😄✔
                    </div>
                    <div id="alert-danger" style="display: inline; color: red; font-weight: bold;"> ❗비밀번호가 다름❗
                    </div>
                </td>

            </tr>
            <tr>
                <td><label class="control-label">이름</label></td>
                <td><input type="text" name="name" value="<?php echo $user_name; ?>" placeholder="이름 2 ~ 20 글자" autocomplete="off"
                           class="form-control" id="name" readonly onfocus="this.removeAttribute('readonly');" required></td>
                <td>
                    <div id="name-available" style="display: none; color: blue; font-weight: bold;"> ✔사용 가능한 이름입니다.</div>
                    <div id="name-disable" style="display: none; color: red; font-weight: bold;"> ❌이름은 한글 또는 영문 (2~20)</div>
                    <div id="previous-name" style="display: inline; color: cadetblue; font-weight: bold;"> ♻원래 이름 유지하기</div>
                </td>
            </tr>
            <tr>
                <td><label class="control-label">전화번호</label></td>
                <td><input type="text" name="phone" value="<?php echo $user_phone; ?>" placeholder="<?php echo $user_phone; ?>" autocomplete="off"
                           id="phone" class="form-control" title="010-1234-1234 형식" maxlength="13"
                           readonly onfocus="this.removeAttribute('readonly');" required></td>
                <td>
                    <div id="check_phone_mention">&nbsp;실시간 전화번호 체크</div>
                    <br></td>
            </tr>
            <tr>
                <td><label class="control-label">User 권한</label></td>
                <td><input type="text" name="level" value="<?php echo $user_level ?>" placeholder="관리자 = 1, 사용자 = 4" autocomplete="off"
                           class="form-control" readonly onfocus="this.removeAttribute('readonly');" required></td>
                <td>
                    <label class="control-label"> </label>
                </td>
            </tr>
            <tr>
                <td><label class="control-label" style="color: dodgerblue;">성별</label></td>
                <td>
                    <input type="text" id="userGender" name="userGender" class="form-control" value="<?php echo $user_gender; ?>"
                           autocomplete="off" readonly />
                </td>
                <td>
                    <label class="control-label" style="color: dodgerblue;"> 수정 불가능</label>
                </td>
            </tr>
            <tr>
                <td><label class="control-label" style="color: dodgerblue;">가입일</label></td>
                <td>
                    <input type="text" id="userReg" name="userReg" class="form-control" value="<?php echo $user_reg_dt; ?>"
                           autocomplete="off" readonly />
                </td>
                <td>
                    <label class="control-label" style="color: dodgerblue;"> 수정 불가능</label>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type="submit" class="btn btn-primary" formaction="/Admin/userInfoUpdate"><span class="glyphicon glyphicon-floppy-save"></span>&nbsp; 업데이트</button>
                </td>
                <td colspan="2" align="center">
                    <a class="btn btn-warning" href="/Admin/index"> <span class="glyphicon glyphicon-remove"></span>&nbsp; 취소</a>
                </td>
            </tr>
        </table>
    </form>
</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.0.min.js"></script>
<script>
    $(document).ready(function () {
        // 전화번호 중복검사 시 필요
        var checkPhoneMention = $('#check_phone_mention');

        // 비밀 번호 글자 수 유효성 검사
        $("#pw1").on("keyup", function () {
            let passwordReg = /^(?=.*[a-zA-Z])((?=.*\d)|(?=.*\W)).{6,20}$/;
            if (!passwordReg.test($("#pw1").val())) {
                $("#check-pw-fail").css('display', 'inline');
                $("#check-pw-success").css('display', 'none');
                $("#collect_password_reg").val('n');
            } else {
                $("#check-pw-fail").css('display', 'none');
                $("#check-pw-success").css('display', 'inline');
                $('#collect_password_reg').val('y');
            }
        });

        // 비밀 번호 일치 검사
        $("#pw2").on("keyup", function () {
            let pwd1 = $("#pw1").val();
            let pwd2 = $("#pw2").val();

            // Null 확인
            if (pwd1 && !pwd2) {

            } else if (pwd1 || pwd2) {
                if (pwd1 === pwd2) {
                    $("#alert-success").css('display', 'inline');
                    $("#alert-danger").css('display', 'none');
                    $('#collect_password').val('y');
                } else {
                    $("#alert-success").css('display', 'none');
                    $("#alert-danger").css('display', 'inline');
                    $('#collect_password').val('n');
                }
            }
        });
        var prevUserName = "<?php echo $user_name; ?>";
        // 이름 유효성 검사
        $("#name").on("keyup", function () {
            // 사용하던 이름이 아니면
            if (prevUserName !== $(this).val()) {
                var nameReg = /^[^0-9][^`~!@#$%^&*|\\\'\";:\/?]{2,20}$/;
                if (!nameReg.test($("#name").val())) {
                    $("#name-disable").css('display', 'inline');
                    $("#name-available").css('display', 'none');
                    $("#previous-name").css('display', 'none');
                    $("#collect_name").val('n');
                } else {
                    $("#name-disable").css('display', 'none');
                    $("#name-available").css('display', 'inline');
                    $("#previous-name").css('display', 'none');
                    $('#collect_name').val('y');
                }
            } else {
                // 이전에 사용하던 이름이라면
                $("#name-disable").css('display', 'none');
                $("#name-available").css('display', 'none');
                $("#previous-name").css('display', 'inline');
                $('#collect_name').val('y');
            }
        });

        var prevPhoneNumber = "<?php echo $user_phone; ?>";
        // 전화번호 중복 검사
        $("#phone").on("keyup", function (e) { //checkId 클래스에 입력을 감지
            // 한글 방지
            if (!(e.keyCode >= 37 && e.keyCode <= 40)) {
                var v = $(this).val();
                $(this).val(v.replace(/[^a-z0-9-]/gi, ''));
            }
            // 사용하던 전화번호이면
            if (prevPhoneNumber === v) {
                checkPhoneMention.html("♻ 이전 전화번호 유지하기");
                checkPhoneMention.css("color", "#0066FF"); // css 입히기
                $('#collect_tell').val('y');
                return;
            }
            var inputPhone = $("#phone").val();
            $.ajax({
                url: "/Membership/checkPhone",
                method: 'POST',
                data: {phone: inputPhone},
                dataType: "json",
                async: false
            }).done(function (data) {
                checkPhoneMention.html(data.mention);
                if (data.status === 'check') {
                    checkPhoneMention.css("color", "#FFB300"); // css 입히기
                    $('#collect_tell').val('n');
                } else if (data.status === 'available') {
                    checkPhoneMention.css("color", "#00FF99"); // css 입히기
                    $('#collect_tell').val('y');
                } else {
                    checkPhoneMention.css("color", "#F00"); // css 입히기
                    $('#collect_tell').val('n');
                }
                return false;
            });
        });

        $("#form_sub").click(function () {
            if ($('#collect_password').val() !== 'y') {
                alert('비밀번호가 일치하지 않습니다.');
                return false;
            } else if ($('#collect_password_reg').val() !== 'y') {
                alert('비밀번호는 8~20자리로 영문, 숫자를 포함해주세요.');
                return false;
            } else if ($('#collect_tell').val() !== 'y') {
                alert('전화번호를 확인해주세요.');
                return false;
            } else if ($('#collect_name').val() !== 'y') {
                alert('이름 형식에 맞게 입력해주세요.');
                return false;
            }

            alert("🎉회원정보 수정이 완료되었습니다!");
            $('#form').submit();
        });
    });
</script>
</body>
</html>


