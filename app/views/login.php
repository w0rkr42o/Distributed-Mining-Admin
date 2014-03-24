<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Miner - Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link href="css/mining_css.css" rel="stylesheet" type="text/css"/>
        <link href="css/login.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
        <script type="text/javascript" src="js/login.js"></script>
    </head>
    <body>
        <header>
            <div id="header_wrapper">
                <div class="banner"></div>
                <div class="navigation">

                </div>
            </div>
        </header>
        <section id="main">
            <div id="wrapper">
                <div class="user-icon"></div>
                <div class="pass-icon"></div>
				<form name="login-form" class="login-form" action="<?=myUrl('login')?>" method="post">
                    <div class="header">
                        <h1>Login - MiAdmin</h1>
                    </div>
                    <div class="content">
                        <input name="username" type="text" class="input username" value="Username" onfocus="this.value = ''" />
                        <input name="password" type="password" class="input password" value="Password" onfocus="this.value = ''" />
                    </div>
                    <div class="footer">
                        <input type="submit" name="login" value="Login" class="button" />
                    </div>
                </form>
                <div id="errormessage">
                    <h4>
                        <?php if(isset($errormessage)){
                            echo $errormessage;
                        }?>
                    </h4>
                </div>
            </div>
        </section>
    </body>
</html>


