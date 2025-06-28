<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION["username"];
    $oldPassword = $_POST["old_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if (strlen($newPassword) < 5) {
        $error = "Mật khẩu mới phải có ít nhất 5 ký tự!";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        $filePath = __DIR__ . "/users/user.xml";
        if (!file_exists($filePath)) {
            $error = "Không tìm thấy file dữ liệu!";
        } else {
            $xml = simplexml_load_file($filePath);
            $userFound = false;

            foreach ($xml->user as $user) {
                if ((string)$user->username === $username) {
                    $userFound = true;

                    if ((string)$user->password !== $oldPassword) {
                        $error = "Mật khẩu cũ không đúng!";
                    } else {
                        // Lưu mật khẩu cũ vào old_password
                        if (!isset($user->old_password)) {
                            $user->addChild("old_password", htmlspecialchars($user->password));
                        } else {
                            $user->old_password = htmlspecialchars($user->password);
                        }

                        // Cập nhật mật khẩu mới
                        $user->password = htmlspecialchars($newPassword);

                        // Ghi lại XML
                        if ($xml->asXML($filePath)) {
                            $success = "✅ Mật khẩu đã được cập nhật!";
                        } else {
                            $error = "❌ Lỗi khi lưu mật khẩu mới!";
                        }
                    }
                    break;
                }
            }

            if (!$userFound) {
                $error = "Tài khoản không tồn tại!";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Đổi mật khẩu</title>
	<link rel="icon" type="image/x-icon" href="assets/Empty.ico">
    <link rel="stylesheet" href="/assets/style-rep.css" />
	<script>
		var password = document.getElementById("password")
  , confirm_password = document.getElementById("confirmPassword");

document.getElementById('signupLogo').src = "https://s3-us-west-2.amazonaws.com/shipsy-public-assets/shipsy/SHIPSY_LOGO_BIRD_BLUE.png";
enableSubmitButton();

function validatePassword() {
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Passwords Don't Match");
    return false;
  } else {
    confirm_password.setCustomValidity('');
    return true;
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;

function enableSubmitButton() {
  document.getElementById('submitButton').disabled = false;
  document.getElementById('loader').style.display = 'none';
}

function disableSubmitButton() {
  document.getElementById('submitButton').disabled = true;
  document.getElementById('loader').style.display = 'unset';
}

function validateSignupForm() {
  var form = document.getElementById('signupForm');
  
  for(var i=0; i < form.elements.length; i++){
      if(form.elements[i].value === '' && form.elements[i].hasAttribute('required')){
        console.log('There are some required fields!');
        return false;
      }
    }
  
  if (!validatePassword()) {
    return false;
  }
  
  onSignup();
}

function onSignup() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    
    disableSubmitButton();
    
    if (this.readyState == 4 && this.status == 200) {
      enableSubmitButton();
    }
    else {
      console.log('AJAX call failed!');
      setTimeout(function(){
        enableSubmitButton();
      }, 1000);
    }
    
  };
  
  xhttp.open("GET", "ajax_info.txt", true);
  xhttp.send();
}
	</script>
</head>
<body>
    <div class="mainDiv">
        <div class="cardStyle">
            <div id="signupLogo">
            </div>

            <h2 class="formTitle">Đổi mật khẩu</h2>

            <?php
                if (!empty($error)) {
                    echo '<p style="color:red;text-align:center;">' . htmlspecialchars($error) . '</p>';
                } elseif (!empty($success)) {
                    echo '<p style="color:green;text-align:center;">' . htmlspecialchars($success) . '</p>';
                }
            ?>

            <form method="POST" action="">
                <div class="inputDiv">
                    <label class="inputLabel" for="old_password">Mật khẩu cũ:</label>
                    <input type="password" id="old_password" name="old_password" required />
                </div>

                <div class="inputDiv">
                    <label class="inputLabel" for="new_password">Mật khẩu mới:</label>
                    <input type="password" id="new_password" name="new_password" required />
                </div>

                <div class="inputDiv">
                    <label class="inputLabel" for="confirm_password">Xác nhận mật khẩu mới:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required />
                </div>

                <div class="buttonWrapper">
                    <button type="submit" class="submitButton">Cập nhật mật khẩu</button>
                </div>
            </form>

            <a href="/chat/index.php" class="back-button" style="display:block; text-align:center; margin-top:20px; color:#065492; text-decoration:none;">
                ← Quay lại
            </a>
        </div>
    </div>
</body>
</html>
