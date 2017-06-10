<?php
// Emails form data to you and the person submitting the form and adds it to a database

 $redirect = "index.html";

function validaEmail($email){  
    $conta = "^[a-zA-Z0-9\._-]+@";
    $domino = "[a-zA-Z0-9\._-]+.";
    $extensao = "([a-zA-Z]{2,4})$";
    $pattern = $conta.$domino.$extensao;
    if (preg_match("/".$pattern."/", $email))
        return true;
    else
        return false;

}

// Test for db
$db = new mysqli("dbinvestidor.mysql.uhserver.com","gustavolizze","2025@gama","dbinvestidor");


$ip = getenv("REMOTE_ADDR");
$data = date("Y/m/d");
$hora = date("H:i:s");
$data_hora = $data . " " . $hora;
$interesse =  $_POST['interesse'];
if ($interesse == 1 ){
    $opcao = 'tecnologia E inovacao';
}
if ($interesse == 2 ){
    $opcao = 'investimento';
}
if($interesse == 3){
    $opcao = 'outros';
}


// Receive and sanitize input
$nome = mysqli_real_escape_string($db, $_POST['nome']);
$snome = mysqli_real_escape_string($db, $_POST['snome']);
$email = mysqli_real_escape_string($db, $_POST['email']);
$outros = mysqli_real_escape_string($db, $_POST['especifique']);

$nome = $nome . " " . $snome;    



if(!validaEmail($email)){  
    header("location:$redirect");
    die;
}


$sql = "SELECT * FROM landing WHERE  email = '{$email}'" ;
$verifica = $db->query( $sql );

if($verifica->num_rows > 0 ){
    echo "E-mail " . $email . " ja esta cadastrado! Por favor, aguarde para receber seu Ebook!"; 
}
else{
    
    $sql = "INSERT INTO dbinvestidor.landing (email, nome, ip, data_hora, interesse, outro) VALUES ('$email','$nome','$ip','$data_hora', '$opcao', '$outros')";
    $result = $db->query($sql);
    
    require_once('phpmailer/class.phpmailer.php');


    $mail = new PHPMailer(); //instancia a classe
 
    $mail->IsMail();//define função
 
    //autenticação
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'investidorinovador@gmail.com';
    $mail->Password = 'Gamaexperience6';
    $mail->Port = '465'; 
    $mail->IsHTML(true);
    $mail->Subject = utf8_decode("Parabéns " .$nome. "! Receba seu EBook.");//assunto do email
    $mail->From = "investidorinovador@gmail.com";//email do remetente
    $mail->FromName ='Investidor Inovador';//nome do remetente 
    $mail->Body = utf8_decode('Olá ' . $nome . ',Obrigado por baixar o nosso Ebook: <strong>Guia prático de Bitcoin, a moeda digital que está revolucionando o mundo dos investimentos. </strong> temos certeza de que esse material vai te ajudar a entender melhor o que é o bitcoin e como ele pode ser uma boa opção para inovar os seus investimentos. <strong>Aproveite o conteúdo ;)</strong> Segue em anexo o seu guia. Caso precise de mais informações, acesse nosso site: www.investidorinovador.com.br . <Strong>Boa leitura e bons investimentos! </strong> Um abraço, <strong>Time Investidor Inovador.</strong> ');
    $mail->AddAddress($email);//email do destinatario
    $file_to_attach = 'pdf/Ebook.pdf';
    $mail->AddAttachment( $file_to_attach , 'Ebook.pdf' );
    if(!$mail->Send()) {
        echo 'Not sent: <pre>'.print_r(error_get_last(), true).'</pre>';
                        }
    else {
        echo 'Por favor, <a href="index.html">verifique sua caixa de entrada para obter o Ebook. O envio do e-mail pode demorar um pouco de acordo com a demanda, obrigado!';
        }

    
}




?>
