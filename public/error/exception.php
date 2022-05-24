<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<head>
<title><?=$kod?>: <?=$opis?> arcelormittal</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<meta name="copyright" content="e-orion.pl" >
<meta name="author" content="e-orion.pl" >
<meta name="editor" content="e-orion.pl" >
<meta name="design" content="e-orion.pl" >
<meta name="robots" content="NOINDEX, NOFOLLOW" >
<link href="/skin/site/css/error.css" media="screen" rel="stylesheet" type="text/css" >

<body>
    <div id="wrapper">
        <div id="logo">
           <img src="skin/site/images/logo.png" alt=""/>
        </div>
        <div id="content">
            <h1>Błąd <?=$kod?>: <span><?=$opis?></span></h1>
            <hr/>
            <h2>Wystąpił błąd aplikacji, prosimy spróbować później</h2>
            <h3>Możliwe, że nie możesz zobaczyć tej strony, ponieważ</h3>
            <ul>
                <li>1. Adres przestał istnieć w systemie.</li>
                <li>2. Adres został wpisany z błędem.</li>
                <li>3. Nie masz uprawnień do obejrzenia tej strony.</li>
                <li>4. Nie można zlokalizować wskazanego zasobu.</li>
                <li>5. Wystąpił błąd podczas wykonywania powierzonego zadania.</li>
            </ul>

            Jeśli problem się powtarza, skontaktuj się z administratorem witryny.
            
            <?php
            if ( true) {
                echo '<br /><br />' . $exception->getMessage() . '<br />'
                   . '<div align="left">Stack Trace:'
                   . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
            } ?>
        </div>
    </div>


</body>
</html>