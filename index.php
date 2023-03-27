<?php

require 'includes/connexion_bdd.php';

// Filtres PHP (utilisés notamment avec filter_var)
// https://www.php.net/manual/fr/filter.filters.php

require 'includes/sujets.php';

$erreurs = [];

if (empty($_POST) === false) {

	// Vérification des données saisies
	if (empty($_POST['email'])) {
		$erreurs['email'] = 'Veuillez saisir une adresse email.';
	} else {
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
			$erreurs['email'] = 'Veuillez saisir une adresse email valide.';
		}
	}

	if (empty($_POST['contenu'])) {
		$erreurs['contenu'] = 'Veuillez saisir un contenu.';
	} else {
		if (strlen($_POST['contenu']) > 2000) {
			$erreurs['contenu'] = 'Le contenu ne doit pas dépasser 2000 caractères.';
		}
	}

	$expressionReguliere = '/[\d\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';

	if (empty($_POST['prenom']) === false) {
		if (preg_match($expressionReguliere, $_POST['prenom'])) {
			$erreurs['prenom'] = 'Le prénom ne doit pas contenir de chiffres et de caractères spéciaux.';
		}
	}

	if (empty($_POST['nom']) === false) {
		if (preg_match($expressionReguliere, $_POST['nom'])) {
			$erreurs['nom'] = 'Le nom ne doit pas contenir de chiffres et de caractères spéciaux.';
		}
	}

	if (isset($sujets[$_POST['sujet']]) === false) {
		$erreurs['sujet'] = 'Veuillez préciser un sujet valide.';
	}


    if (empty($erreurs)) {
        try {
            $requeteInsertion = $connexion->prepare('INSERT INTO contact (contact_nom, contact_prenom, contact_email, contact_sujet, contact_contenu) VALUES (:contact_nom, :contact_prenom, :contact_email, :contact_sujet, :contact_contenu)');
            $requeteInsertion->bindParam(':contact_nom', $_POST['nom']);
            $requeteInsertion->bindParam(':contact_prenom', $_POST['prenom']);
            $requeteInsertion->bindParam(':contact_email', $_POST['email']);
            $requeteInsertion->bindParam(':contact_sujet', $_POST['sujet']);
            $requeteInsertion->bindParam(':contact_contenu', $_POST['contenu']);

            $requeteInsertion->execute();

            echo 'Votre demande a bien été prise en compte.';
        } catch (\Exception $exception) {
            echo 'Erreur lors de l\'ajout de la demande de contact.';
            // Debug de l'erreur :
            // var_dump($exception->getMessage());
        }
    }
}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Contactez-moi</title>
</head>
<body>

	<?php
		require 'includes/header.php';
	?>

	<form action="#" method="POST">
		
		<div>
			<label for="email">Email <span style="color: red;">*</span></label>
			<?= isset($erreurs['email']) ? $erreurs['email'] : null; ?>
			<input type="email" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : null; ?>">
		</div>

		<div>
			<label for="prenom">Prénom</label>
			<input type="text" name="prenom" value="<?= isset($_POST['prenom']) ? $_POST['prenom'] : null; ?>">
			<?= isset($erreurs['prenom']) ? $erreurs['prenom'] : null; ?>
		</div>

		<div>
			<label for="nom">Nom</label>
			<input type="text" name="nom" value="<?= isset($_POST['nom']) ? $_POST['nom'] : null; ?>">
			<?= isset($erreurs['nom']) ? $erreurs['nom'] : null; ?>
		</div>

		<div>
			<label for="sujet">Sujet <span style="color: red;">*</span></label>
			<select name="sujet" id="sujet">
				<?php foreach ($sujets as $valeur => $nom) { ?>
				<option <?php if (isset($_POST['sujet']) && $_POST['sujet'] === $valeur) { echo 'selected'; } ?> value="<?= $valeur ?>"><?= $nom ?></option>
				<?php } ?>
			</select>
			<?= isset($erreurs['sujet']) ? $erreurs['sujet'] : null; ?>
		</div>

		<div>
			<label for="contenu">Contenu <span style="color: red;">*</span></label>
			<textarea name="contenu"><?= isset($_POST['contenu']) ? $_POST['contenu'] : 'Votre contenu...'; ?></textarea>
			<?= isset($erreurs['contenu']) ? $erreurs['contenu'] : null; ?>
		</div>

		<div>
			<input type="submit" name="validation">
		</div>
	</form>
  <a href="https://www.google.fr">Lien google</a>

</body>
</html>
