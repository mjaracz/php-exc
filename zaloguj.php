<?php

	session_start();

	if ((!isset($_POST['login'])) || (!isset($_POST['haslo'])))
	{

		header('Location: index.php');
		exit(); 

	}

	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);

	
	try
	{

		
		$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
		if ($polaczenie->connect_errno!=0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		
		else
		{

			$login = $_POST['login'];
			$haslo = $_POST['haslo'];

			$login = htmlentities($login, ENT_QUOTES, "UTF-8");

			$rezultat = $polaczenie->query(sprintf("SELECT * FROM uzytkownicy WHERE user='%s'",
			mysqli_real_escape_string($polaczenie,$login)));

			if (!$rezultat) throw new Exception($polaczenie->error);

			else
			{
				$ile_userow = $rezultat->num_rows;
				if ($ile_userow > 0)
				{
					$wiersz = $rezultat->fetch_assoc();

					if (password_verify($haslo, $wiersz['pass']))
					{
						$_SESSION['zalogowany'] = true;

						$_SESSION['id'] = $wiersz['id'];
						$_SESSION['user'] = $wiersz['user'];
						$_SESSION['drewno'] = $wiersz['drewno'];
						$_SESSION['kamien'] = $wiersz['kamien'];
						$_SESSION['zboze'] = $wiersz['zboze'];
						$_SESSION['email'] = $wiersz['email'];
						$_SESSION['dnipremium'] = $wiersz['dnipremium'];

						unset($_SESSION['blad']);
						$rezultat->free_result();
						header('Location: gra.php');

						echo $login . $haslo;
					}
					else
					{
						$_SESSION['blad'] = '<span style ="color: red">Nieprawidłowe hasło!</span>';
						header('Location: index.php');
					}
				} 
				else
				{
					$_SESSION['blad'] = '<span style ="color: red">Nieprawidłowy login lub hasło!</span>';
					header('Location: index.php');
				}

			}

			$polaczenie->close();
		}
	}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Serwer się zjebał!! Wybaczcie te drobne niedogodności,  zarejestruj się w innym terminie.</span>';
			echo '<br/><span style="color:#b71c1c;"> Info. dla wtajemniczonych:</span>'.$e;
		}
?>