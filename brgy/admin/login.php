<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../DBConnection.php');
$conn = new DBConnection(); // also loads barangay name/branding into $_SESSION['system_info']
if(isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0){
    header("Location: index.php");
    exit;
}
$barangay_name = isset($_SESSION['system_info']['barangay_name']) ? $_SESSION['system_info']['barangay_name'] : 'Barangay';
// Optional: put a path to a barangay/city photo here (falls back to a plain
// gradient if the file doesn't exist).
$hero_image = isset($_SESSION['system_info']['hero_image']) ? $_SESSION['system_info']['hero_image'] : '../assets/img/hero-city.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | <?php echo htmlspecialchars($barangay_name) ?></title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/script.js"></script>
    <style>
        :root{
            --ink-dark:#0e1a1a;
            --ink-darker:#0a1414;
            --accent:#1b3b3a;
            --accent-light:#2f5f5c;
        }
        html, body{
            height:100%;
            margin:0;
        }
        body{
            font-family:'Segoe UI', Roboto, Arial, sans-serif;
            background:var(--ink-dark);
        }
        .login-wrap{
            min-height:100vh;
            display:flex;
        }
        .login-left{
            flex:0 0 55%;
            max-width:55%;
            background:var(--ink-dark);
            display:flex;
            align-items:center;
            padding:6vw;
            box-sizing:border-box;
        }
        .login-left-inner{
            width:100%;
            max-width:420px;
        }
        .login-eyebrow{
            color:#eef1ef;
            font-size:1.9rem;
            font-weight:300;
            margin:0;
            line-height:1.25;
        }
        .login-title{
            color:#ffffff;
            font-size:1.9rem;
            font-weight:700;
            margin:0 0 18px 0;
            line-height:1.25;
        }
        .login-subtext{
            color:#9fb0ae;
            font-size:0.85rem;
            line-height:1.5;
            margin-bottom:2rem;
            max-width:340px;
        }
        .login-form .form-group{
            margin-bottom:14px;
        }
        .login-form input.form-control{
            border:none;
            border-radius:2px;
            padding:12px 14px;
            font-size:0.85rem;
            background:#f4f6f5;
            color:#333;
        }
        .login-form input.form-control::placeholder{
            color:#8a8a8a;
        }
        .login-form input.form-control:focus{
            box-shadow:0 0 0 2px var(--accent-light);
            outline:none;
        }
        .btn-login{
            background:var(--accent);
            color:#fff;
            border:none;
            border-radius:2px;
            padding:10px 28px;
            font-size:0.78rem;
            letter-spacing:1px;
            font-weight:600;
            text-transform:uppercase;
            margin-top:8px;
        }
        .btn-login:hover{
            background:var(--accent-light);
            color:#fff;
        }
        .btn-login:disabled{
            opacity:0.6;
        }
        .login-right{
            flex:1;
            position:relative;
            overflow:hidden;
            background:
                linear-gradient(90deg, var(--ink-dark) 0%, rgba(14,26,26,0.55) 35%, rgba(14,26,26,0.15) 100%),
                url('<?php echo htmlspecialchars($hero_image) ?>') center/cover no-repeat,
                linear-gradient(135deg, #16302f, #0a1414);
        }
        .login-right .socials{
            position:absolute;
            right:18px;
            top:50%;
            transform:translateY(-50%);
            display:flex;
            flex-direction:column;
            gap:10px;
        }
        .login-right .socials a{
            width:34px;
            height:34px;
            border-radius:50%;
            background:rgba(255,255,255,0.12);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            text-decoration:none;
            font-size:0.85rem;
            backdrop-filter:blur(2px);
        }
        .login-right .socials a:hover{
            background:rgba(255,255,255,0.25);
        }
        .pop_msg{
            padding:8px 12px;
            font-size:0.8rem;
            border-radius:2px;
            margin-bottom:10px;
        }
        @media (max-width: 767px){
            .login-wrap{ flex-direction:column; }
            .login-left{ flex:1 1 auto; max-width:100%; padding:10vw 8vw; }
            .login-right{ min-height:220px; }
            .login-right .socials{ display:none; }
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-left">
            <div class="login-left-inner">
                <p class="login-eyebrow">Welcome to</p>
                <h1 class="login-title"><?php echo htmlspecialchars($barangay_name) ?></h1>
                <p class="login-subtext">This website was created to provide your needs from the comfort of your home!</p>

                <form action="" id="login-form" class="login-form">
                    <div class="form-group">
                        <input type="text" id="username" autofocus name="username" class="form-control" placeholder="Enter Username..." required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password..." required>
                    </div>
                    <button type="submit" class="btn btn-login">Login</button>
                </form>
            </div>
        </div>
        <div class="login-right">
            <div class="socials">
                <a href="#" target="_blank" rel="noopener" aria-label="Twitter">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M23 4.9c-.8.4-1.7.6-2.6.8.9-.6 1.6-1.5 2-2.5-.9.5-1.9.9-2.9 1.1-.8-.9-2-1.5-3.3-1.5-2.5 0-4.5 2-4.5 4.5 0 .4 0 .7.1 1-3.7-.2-7.1-2-9.3-4.7-.4.7-.6 1.4-.6 2.3 0 1.6.8 3 2 3.8-.7 0-1.4-.2-2-.6v.1c0 2.2 1.6 4 3.6 4.4-.4.1-.8.2-1.2.2-.3 0-.6 0-.8-.1.6 1.8 2.3 3.1 4.2 3.1-1.5 1.2-3.5 2-5.6 2-.4 0-.7 0-1.1-.1 2 1.3 4.4 2 7 2 8.4 0 13-7 13-13v-.6c.9-.6 1.6-1.4 2.2-2.3z"/></svg>
                </a>
                <a href="#" target="_blank" rel="noopener" aria-label="Facebook">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z"/></svg>
                </a>
            </div>
        </div>
    </div>
</body>
<script>
    $(function(){
        $('#login-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('Loging in...')
            $.ajax({
                url:'./../Actions.php?a=login',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Login')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        setTimeout(() => {
                            location.replace('./');
                        }, 2000);
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Login')
                }
            })
        })
    })
</script>
</html>