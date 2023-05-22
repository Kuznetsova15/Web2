  }
  else {
    $powrs=$_POST['super'];
    $apw=array(
      "inv_v"=>0,
      "walk_v"=>0,
      "fly_v"=>0,
    );
  foreach($powrs as $pwer){
    if($pwer=='inv'){setcookie('inv_v', 1, time() + 12 * 30 * 24 * 60 * 60); $apw['inv_v']=1;} 
    if($pwer=='walk'){setcookie('walk_v', 1, time() + 12*30 * 24 * 60 * 60);$apw['walk_v']=1;} 
    if($pwer=='fly'){setcookie('fly_v', 1, time() + 12*30 * 24 * 60 * 60);$apw['fly_v']=1;} 
    }
  foreach($apw as $c=>$val){
    if($val==0){
      setcookie($c,'',100000);
    }
  }
}
  
  if ((empty($_POST['bio'])) || (!preg_match($bioregex,$_POST['bio']))) {
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    setcookie('bio_value', '', 100000);
    $errors = TRUE;
  }
  else {
    setcookie('bio_value', $_POST['bio'], time() + 12 * 30 * 24 * 60 * 60);
    setcookie('bio_error', '', 100000);
  }
  
  if(!isset($_POST['check-1'])){
    setcookie('check_error','1',time()+  24 * 60 * 60);
    setcookie('check_value', '', 100000);
    $errors=TRUE;
  }
  else{
    setcookie('check_value', TRUE,time()+ 12 * 30 * 24 * 60 * 60);
    setcookie('check_error','',100000);
  }

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('name_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('year_error', '', 100000);
    setcookie('pol_error', '', 100000);
    setcookie('limb_error', '', 100000);
    setcookie('super_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('check_error', '', 100000);
  }
  
  $name = $_POST['name'];
  $email = $_POST['email'];
  $birth_year = $_POST['year'];
  $pol = $_POST['radio-1'];
  $limbs = intval($_POST['radio-2']);
  $superpowers = $_POST['super'];
  $bio= $_POST['bio'];

  // Сохранение в БД.
$user = 'u52978';
$pass = '4644833';
  $db = new PDO('mysql:host=localhost;dbname=u52978', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
   try {
    $stmt = $db->prepare("INSERT INTO form SET name=:name, email=:email, year=:byear, pol=:pol, limbs=:limbs, bio=:bio");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':byear', $birth_year);
    $stmt->bindParam(':pol', $pol);
    $stmt->bindParam(':limbs', $limbs);
    $stmt->bindParam(':bio', $bio);
    if($stmt->execute()==false){
    print_r($stmt->errorCode());
    print_r($stmt->errorInfo());
    exit();
    }
    $id = $db->lastInsertId();
    $sppe= $db->prepare("INSERT INTO super SET name=:name, per_id=:person");
    $sppe->bindParam(':person', $id);
    foreach($superpowers as $inserting){
  	$sppe->bindParam(':name', $inserting);
  	if($sppe->execute()==false){
	    print_r($sppe->errorCode());
	    print_r($sppe->errorInfo());
	    exit();
  	}
    }
  }
  catch(PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
  }

  // Сохраняем куку с признаком успешного сохранения.
  setcookie('save', '1');

  // Делаем перенаправление.
  header('Location: index.php');
}
